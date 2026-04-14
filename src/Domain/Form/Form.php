<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\CaptchaManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\PostSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSetDataEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\SubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\ValidationErrorEvent;
use Iriven\PhpFormGenerator\Domain\Validation\Validator;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ArrayDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ObjectDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\PropertyAccess\PropertyAccessor;

final class Form
{
    private bool $submitted = false;
    private bool $valid = true;

    /** @var array<string, mixed> */
    private array $values = [];

    /** @var array<string, array<int, string>> */
    private array $errors = [];

    /** @var array<int, ConstraintInterface> */
    private array $formConstraints = [];

    private PropertyAccessor $accessor;

    /**
     * @param array<string, FieldConfig> $fields
     * @param array<int, Fieldset> $fieldsets
     * @param array<int, ConstraintInterface> $formConstraints
     */
    /**
     * @param array<string, FieldConfig> $fields
     * @param array<string, mixed> $options
     * @param array<int, Fieldset> $fieldsets
     * @param array<int, ConstraintInterface> $formConstraints
     */
    public function __construct(
        private readonly string $name,
        private readonly array $fields,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly array $options = [],
        private mixed $data = null,
        private readonly array $fieldsets = [],
        array $formConstraints = [],
    ) {
        $this->formConstraints = $formConstraints;
        $this->accessor = new PropertyAccessor();
        $this->dispatch('form.pre_set_data', new PreSetDataEvent($this, $this->data));
        $this->initializeValues();
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function initializeValues(): void
    {
        foreach ($this->fields as $name => $field) {
            $raw = $this->readDataValue($name, $field);

            if ($field->collection) {
                $this->values[$name] = is_array($raw) ? $raw : [];
                continue;
            }

            if ($field->compound) {
                $this->values[$name] = is_array($raw) ? $raw : [];
                continue;
            }

            $value = $raw;
            foreach ($field->transformers as $transformer) {
                $value = $transformer->transform($value);
            }
            $this->values[$name] = $value;
        }
    }

    private function readDataValue(string $name, FieldConfig $field): mixed
    {
        if (array_key_exists('data', $field->options)) {
            return $field->options['data'];
        }

        if (is_array($this->data)) {
            return $this->data[$name] ?? null;
        }

        if (is_object($this->data)) {
            return $this->accessor->getValue($this->data, $name);
        }

        return null;
    }

    public function handleRequest(RequestInterface $request): void
    {
        if (strtoupper((string) ($this->options['method'] ?? 'POST')) !== $request->getMethod()) {
            return;
        }

        $payload = $request->get($this->name, []);
        if (!is_array($payload)) {
            return;
        }

        $this->submitted = true;

        $preSubmit = new PreSubmitEvent($this, $payload);
        $this->dispatch('form.pre_submit', $preSubmit);
        $payload = is_array($preSubmit->getData()) ? $preSubmit->getData() : $payload;

        if (($this->options['csrf_protection'] ?? false) === true) {
            $tokenField = (string) ($this->options['csrf_field_name'] ?? '_token');
            $tokenId = (string) ($this->options['csrf_token_id'] ?? $this->name);
            $csrfManager = $this->options['csrf_manager'] ?? null;

            if ($csrfManager !== null && !$csrfManager->isTokenValid($tokenId, is_string($payload[$tokenField] ?? null) ? $payload[$tokenField] : null)) {
                $this->errors['_form'][] = 'Invalid CSRF token.';
                $this->valid = false;
            }
        }

        foreach ($this->fields as $name => $field) {
            $raw = $payload[$name] ?? null;
            $path = $name;
            $this->values[$name] = $this->submitField($field, $raw, $path);
        }

        $this->validateFormConstraints();

        $this->dispatch('form.submit', new SubmitEvent($this, $this->values, ['payload' => $payload]));

        if ($this->valid) {
            $this->mapData();
        }

        $this->dispatch('form.post_submit', new PostSubmitEvent($this, $this->data, ['valid' => $this->valid]));
    }

    private function submitField(FieldConfig $field, mixed $raw, string $path): mixed
    {
        if ($field->collection) {
            return $this->submitCollection($field, is_array($raw) ? $raw : [], $path);
        }

        if ($field->compound) {
            return $this->submitCompound($field, is_array($raw) ? $raw : [], $path);
        }

        if ($field->typeClass === 'Iriven\\PhpFormGenerator\\Domain\\Field\\CheckboxType' && $raw === null) {
            $raw = false;
        }

        $value = $raw;
        foreach ($field->transformers as $transformer) {
            $value = $transformer->reverseTransform($value);
        }

        if (is_a($field->typeClass, 'Iriven\\PhpFormGenerator\\Domain\\Field\\CaptchaType', true)) {
            $this->validateCaptchaField($field, is_string($raw) ? $raw : null, $path);
        }

        $this->applyConstraintErrors($path, $value, $field->constraints);

        return $value;
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function submitCompound(FieldConfig $field, array $raw, string $path): array
    {
        $result = [];
        foreach ($field->children as $childName => $child) {
            $childPath = $path . '.' . $childName;
            $result[$childName] = $this->submitField($child, $raw[$childName] ?? null, $childPath);
        }

        $this->applyConstraintErrors($path, $result, $field->constraints);

        return $result;
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array<int, mixed>
     */
    private function submitCollection(FieldConfig $field, array $raw, string $path): array
    {
        $items = [];
        $entryType = $field->entryType;

        foreach ($raw as $index => $row) {
            if (!is_array($row)) {
                $row = ['value' => $row];
            }

            if ($entryType !== null && is_subclass_of($entryType, FormTypeInterface::class)) {
                $builder = new FormBuilder($field->name . '_entry', null, $field->entryOptions + ['event_dispatcher' => $this->eventDispatcher]);
                $type = new $entryType();
                $resolver = new OptionsResolver();
                $type->configureOptions($resolver);
                $resolved = $resolver->resolve($field->entryOptions);
                $type->buildForm($builder, $resolved);
                $entryValues = [];
                foreach ($builder->all() as $childName => $child) {
                    $entryValues[$childName] = $this->submitField($child, $row[$childName] ?? null, $path . '.' . (string) $index . '.' . $childName);
                }
                $items[] = $entryValues;
                continue;
            }

            if ($entryType !== null && class_exists($entryType)) {
                $transformers = method_exists($entryType, 'defaultTransformers') ? $entryType::defaultTransformers() : [];
                $entryField = new FieldConfig((string) $index, $entryType, $field->entryOptions, $field->constraints, $transformers);
                $items[] = $this->submitField($entryField, $row, $path . '.' . (string) $index);
                continue;
            }

            $items[] = $row;
        }

        if (($field->options['allow_delete'] ?? false) !== true) {
            $items = array_values($items);
        }

        $this->applyConstraintErrors($path, $items, $field->constraints);

        return $items;
    }

    /** @param array<int, ConstraintInterface> $constraints */
    private function applyConstraintErrors(string $path, mixed $value, array $constraints): void
    {
        $errors = (new Validator())->validate($value, $constraints, [
            'values' => $this->values,
            'path' => $path,
            'data' => $this->data,
            'form' => $this,
        ]);

        if ($errors === []) {
            return;
        }

        $this->errors[$path] = $errors;
        $this->valid = false;
        $this->dispatch('form.validation_error', new ValidationErrorEvent($this, $value, ['path' => $path, 'errors' => $errors]));
    }


    private function validateCaptchaField(FieldConfig $field, ?string $input, string $path): void
    {
        $captchaManager = $this->options['captcha_manager'] ?? null;
        if (!$captchaManager instanceof CaptchaManagerInterface) {
            $this->errors[$path][] = 'Captcha manager is not configured.';
            $this->valid = false;
            return;
        }

        $minLength = max(5, (int) ($field->options['min_length'] ?? 5));
        $maxLength = min(8, max($minLength, (int) ($field->options['max_length'] ?? 8)));

        if ($input === null || $input === '' || preg_match('/^[A-Za-z0-9]{' . $minLength . ',' . $maxLength . '}$/', $input) !== 1) {
            $this->errors[$path][] = sprintf('Captcha must be alphanumeric and contain between %d and %d characters.', $minLength, $maxLength);
            $this->valid = false;
            return;
        }

        $key = $this->name . '.' . $field->name;
        if (!$captchaManager->isCodeValid($key, $input)) {
            $this->errors[$path][] = 'Invalid captcha.';
            $this->valid = false;
        }
    }

    private function validateFormConstraints(): void
    {
        if ($this->formConstraints === []) {
            return;
        }

        $errors = (new Validator())->validate($this->values, $this->formConstraints, [
            'values' => $this->values,
            'data' => $this->data,
            'form' => $this,
        ]);

        if ($errors !== []) {
            $this->errors['_form'] = array_merge($this->errors['_form'] ?? [], $errors);
            $this->valid = false;
        }
    }

    private function mapData(): void
    {
        if (is_array($this->data) || $this->data === null) {
            $mapper = new ArrayDataMapper();
            $this->data = $mapper->map(is_array($this->data) ? $this->data : [], $this->values);
            return;
        }

        if (is_object($this->data)) {
            $mapper = new ObjectDataMapper();
            $this->data = $mapper->map($this->data, $this->values);
        }
    }

    public function getData(): mixed
    {
        return $this->data ?? $this->values;
    }

    /** @return array<string, mixed> */
    public function getSubmittedValues(): array
    {
        return $this->values;
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    public function isValid(): bool
    {
        return $this->submitted && $this->valid;
    }

    /** @return array<string, array<int, string>> */
    public function getErrors(): array
    {
        return $this->errors;
    }

public function createView(): FormView
{
    return (new FormViewBuilder())->build($this);
}

/** @return array<string, FieldConfig> */
public function fields(): array
{
    return $this->fields;
}

/** @return array<string, mixed> */
public function options(): array
{
    return $this->options;
}

/** @return array<int, Fieldset> */
public function fieldsets(): array
{
    return $this->fieldsets;
}

/** @return array<string, mixed> */
public function submittedValues(): array
{
    return $this->values;
}

/** @return array<string, array<int, string>> */
public function errors(): array
{
    return $this->errors;
}

public function eventDispatcher(): EventDispatcherInterface
{
    return $this->eventDispatcher;
}

    public function dispatch(string $eventName, object $event): void
    {
        $this->eventDispatcher->dispatch($eventName, $event);
    }
}

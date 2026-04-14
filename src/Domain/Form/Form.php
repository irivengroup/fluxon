<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\PostSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSetDataEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\SubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\ValidationErrorEvent;
use Iriven\PhpFormGenerator\Domain\Validation\Validator;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ArrayDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ObjectDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\PropertyAccess\PropertyAccessor;

final class Form
{
    private bool $submitted = false;
    private bool $valid = true;

    /** @var array<string, mixed> */
    private array $values = [];

    /** @var array<string, list<string>> */
    private array $errors = [];

    /** @var array<string, list<callable>> */
    private array $eventListeners = [];

    /** @var list<ConstraintInterface> */
    private array $formConstraints = [];

    private PropertyAccessor $accessor;

    /**
     * @param array<string, FieldConfig> $fields
     * @param list<Fieldset> $fieldsets
     * @param array<string, list<callable>> $eventListeners
     * @param list<ConstraintInterface> $formConstraints
     */
    public function __construct(
        private readonly string $name,
        private readonly array $fields,
        private mixed $data = null,
        private readonly array $options = [],
        private readonly array $fieldsets = [],
        array $eventListeners = [],
        array $formConstraints = [],
    ) {
        $this->eventListeners = $eventListeners;
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

        $this->applyConstraintErrors($path, $value, $field->constraints);

        return $value;
    }

    /** @param array<string, mixed> $raw */
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

    /** @param array<int|string, mixed> $raw */
    private function submitCollection(FieldConfig $field, array $raw, string $path): array
    {
        $items = [];
        $entryType = $field->entryType;

        foreach ($raw as $index => $row) {
            if (!is_array($row)) {
                $row = ['value' => $row];
            }

            if ($entryType !== null && is_subclass_of($entryType, 'Iriven\\PhpFormGenerator\\Domain\\Contract\\FormTypeInterface')) {
                $builder = new FormBuilder($field->name . '_entry', null, $field->entryOptions);
                $type = new $entryType();
                $resolved = $type->configureOptions($field->entryOptions);
                $type->buildForm($builder, $resolved + $field->entryOptions);
                $entryValues = [];
                foreach ($builder->all() as $childName => $child) {
                    $entryValues[$childName] = $this->submitField($child, $row[$childName] ?? null, $path . '.' . (string) $index . '.' . $childName);
                }
                $items[] = $entryValues;
                continue;
            }

            if ($entryType !== null && class_exists($entryType)) {
                $entryField = new FieldConfig((string) $index, $entryType, $field->entryOptions, $field->constraints, method_exists($entryType, 'defaultTransformers') ? $entryType::defaultTransformers() : []);
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

    /** @param list<ConstraintInterface> $constraints */
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

    /** @return array<string, list<string>> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function createView(): FormView
    {
        $children = [];
        foreach ($this->fields as $name => $field) {
            $fullName = $this->name . '[' . $name . ']';
            $children[] = $this->createFieldView($name, $field, $fullName, 'form_' . $name, $this->values[$name] ?? null, $name);
        }

        $vars = [
            'method' => strtoupper((string) ($this->options['method'] ?? 'POST')),
            'action' => (string) ($this->options['action'] ?? ''),
            'attr' => $this->options['attr'] ?? [],
        ];

        if (($this->options['csrf_protection'] ?? false) === true) {
            $csrfManager = $this->options['csrf_manager'] ?? null;
            $tokenField = (string) ($this->options['csrf_field_name'] ?? '_token');
            $tokenId = (string) ($this->options['csrf_token_id'] ?? $this->name);
            if ($csrfManager !== null) {
                $children[] = new FormView(
                    $tokenField,
                    $this->name . '[' . $tokenField . ']',
                    'form_' . $tokenField,
                    'hidden',
                    $csrfManager->generateToken($tokenId),
                    ['label' => $tokenField, 'type_class' => 'hidden'],
                    [],
                    $this->errors[$tokenField] ?? [],
                );
            }
        }

        return new FormView(
            $this->name,
            $this->name,
            $this->name,
            'form',
            null,
            $vars,
            $children,
            $this->errors['_form'] ?? [],
            $this->fieldsets,
            $this->submitted,
            $this->valid,
        );
    }

    private function createFieldView(string $name, FieldConfig $field, string $fullName, string $id, mixed $value, string $errorPath): FormView
    {
        $vars = $field->options;
        $vars['label'] = $field->options['label'] ?? ucfirst($name);
        $vars['attr'] = $field->options['attr'] ?? [];
        $vars['type_class'] = $field->typeClass;

        if ($field->collection) {
            $children = [];
            if (is_array($value)) {
                foreach ($value as $index => $row) {
                    $entryChildren = [];
                    if ($field->entryType !== null && is_subclass_of($field->entryType, 'Iriven\\PhpFormGenerator\\Domain\\Contract\\FormTypeInterface')) {
                        $builder = new FormBuilder($name . '_entry', null, $field->entryOptions);
                        $entry = new ($field->entryType)();
                        $resolved = $entry->configureOptions($field->entryOptions);
                        $entry->buildForm($builder, $resolved + $field->entryOptions);

                        foreach ($builder->all() as $childName => $child) {
                            $entryChildren[] = $this->createFieldView(
                                $childName,
                                $child,
                                $fullName . '[' . $index . '][' . $childName . ']',
                                $id . '_' . $index . '_' . $childName,
                                $row[$childName] ?? null,
                                $errorPath . '.' . (string) $index . '.' . $childName,
                            );
                        }
                    }

                    $children[] = new FormView(
                        (string) $index,
                        $fullName . '[' . $index . ']',
                        $id . '_' . $index,
                        'collection_entry',
                        $row,
                        ['label' => (string) $index],
                        $entryChildren,
                        $this->errors[$errorPath . '.' . (string) $index] ?? [],
                    );
                }
            }

            if (($field->options['prototype'] ?? false) === true && $field->entryType !== null && is_subclass_of($field->entryType, 'Iriven\\PhpFormGenerator\\Domain\\Contract\\FormTypeInterface')) {
                $builder = new FormBuilder($name . '_prototype', null, $field->entryOptions);
                $entry = new ($field->entryType)();
                $resolved = $entry->configureOptions($field->entryOptions);
                $entry->buildForm($builder, $resolved + $field->entryOptions);
                $prototypeChildren = [];
                foreach ($builder->all() as $childName => $child) {
                    $prototypeChildren[] = $this->createFieldView(
                        $childName,
                        $child,
                        $fullName . '[__name__][' . $childName . ']',
                        $id . '__name__' . '_' . $childName,
                        null,
                        $errorPath . '.__name__.' . $childName,
                    );
                }
                $vars['prototype_view'] = new FormView('__name__', $fullName . '[__name__]', $id . '__name__', 'collection_entry', null, [], $prototypeChildren, []);
            }

            return new FormView($name, $fullName, $id, 'collection', $value, $vars, $children, $this->errors[$errorPath] ?? []);
        }

        if ($field->compound) {
            $children = [];
            foreach ($field->children as $childName => $child) {
                $children[] = $this->createFieldView(
                    $childName,
                    $child,
                    $fullName . '[' . $childName . ']',
                    $id . '_' . $childName,
                    is_array($value) ? ($value[$childName] ?? null) : null,
                    $errorPath . '.' . $childName,
                );
            }

            return new FormView($name, $fullName, $id, 'compound', $value, $vars, $children, $this->errors[$errorPath] ?? []);
        }

        return new FormView($name, $fullName, $id, $field->typeClass, $value, $vars, [], $this->errors[$errorPath] ?? []);
    }

    public function dispatch(string $eventName, object $event): void
    {
        foreach ($this->eventListeners[$eventName] ?? [] as $listener) {
            $listener($event);
        }
    }
}

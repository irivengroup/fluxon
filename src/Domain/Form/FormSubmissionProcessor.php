<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\PostSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\SubmitEvent;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\Translation\TranslatorInterface;

final class FormSubmissionProcessor
{
    public function __construct(
        private readonly FormValidationProcessor $validationProcessor = new FormValidationProcessor(),
        private readonly FormDataMappingProcessor $mappingProcessor = new FormDataMappingProcessor(),
    ) {
    }

    public function handleRequest(Form $form, RequestInterface $request): void
    {
        if (!$this->requestMatchesFormMethod($form, $request)) {
            return;
        }

        $payload = $this->extractPayload($form, $request);
        if ($payload === null) {
            return;
        }

        $form->setSubmitted(true);
        $payload = $this->dispatchPreSubmit($form, $payload);
        $this->validateCsrf($form, $payload);
        $this->submitAllFields($form, $payload);
        $this->validationProcessor->validateFormConstraints($form);
        $this->dispatchSubmit($form, $payload);
        $this->mapIfValid($form);
        $this->dispatchPostSubmit($form);
    }

    private function requestMatchesFormMethod(Form $form, RequestInterface $request): bool
    {
        return strtoupper((string) ($form->options()['method'] ?? 'POST')) === $request->getMethod();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPayload(Form $form, RequestInterface $request): ?array
    {
        $payload = $request->get($form->getName(), []);

        return is_array($payload) ? $payload : null;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function dispatchPreSubmit(Form $form, array $payload): array
    {
        $preSubmit = new PreSubmitEvent($form, $payload);
        $form->dispatch('form.pre_submit', $preSubmit);

        return is_array($preSubmit->getData()) ? $preSubmit->getData() : $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function submitAllFields(Form $form, array $payload): void
    {
        foreach ($form->fields() as $name => $field) {
            $raw = $payload[$name] ?? null;
            $form->setSubmittedValue($name, $this->submitField($form, $field, $raw, $name));
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function dispatchSubmit(Form $form, array $payload): void
    {
        $form->dispatch('form.submit', new SubmitEvent($form, $form->submittedValues(), ['payload' => $payload]));
    }

    private function mapIfValid(Form $form): void
    {
        if ($form->isCurrentlyValid()) {
            $this->mappingProcessor->map($form);
        }
    }

    private function dispatchPostSubmit(Form $form): void
    {
        $form->dispatch('form.post_submit', new PostSubmitEvent($form, $form->rawData(), ['valid' => $form->isCurrentlyValid()]));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function validateCsrf(Form $form, array $payload): void
    {
        if (($form->options()['csrf_protection'] ?? false) !== true) {
            return;
        }

        $tokenField = (string) ($form->options()['csrf_field_name'] ?? '_token');
        $tokenId = (string) ($form->options()['csrf_token_id'] ?? $form->getName());
        $csrfManager = $form->options()['csrf_manager'] ?? null;

        if ($csrfManager !== null && !$csrfManager->isTokenValid($tokenId, is_string($payload[$tokenField] ?? null) ? $payload[$tokenField] : null)) {
            $form->appendError('_form', $this->trans($form, 'csrf.invalid', 'Invalid CSRF token.'));
            $form->setValid(false);
        }
    }

    private function submitField(Form $form, FieldConfig $field, mixed $raw, string $path): mixed
    {
        if ($field->collection) {
            return $this->submitCollection($form, $field, is_array($raw) ? $raw : [], $path);
        }

        if ($field->compound) {
            return $this->submitCompound($form, $field, is_array($raw) ? $raw : [], $path);
        }

        $normalizedRaw = $this->normalizeScalarFieldRawValue($field, $raw);
        $value = $this->reverseTransform($field->transformers, $normalizedRaw);
        $this->validateSpecializedField($form, $field, $normalizedRaw, $path);
        $this->validationProcessor->applyConstraintErrors($form, $path, $value, $field->constraints);

        return $value;
    }

    private function normalizeScalarFieldRawValue(FieldConfig $field, mixed $raw): mixed
    {
        if ($field->typeClass === 'Iriven\PhpFormGenerator\Domain\Field\CheckboxType' && $raw === null) {
            return false;
        }

        return $raw;
    }

    private function validateSpecializedField(Form $form, FieldConfig $field, mixed $raw, string $path): void
    {
        if (is_a($field->typeClass, 'Iriven\PhpFormGenerator\Domain\Field\CaptchaType', true)) {
            $this->validationProcessor->validateCaptchaField($form, $field, is_string($raw) ? $raw : null, $path);
        }
    }

    /**
     * @param array<int, DataTransformerInterface> $transformers
     */
    private function reverseTransform(array $transformers, mixed $raw): mixed
    {
        $value = $raw;
        foreach ($transformers as $transformer) {
            $value = $transformer->reverseTransform($value);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private function submitCompound(Form $form, FieldConfig $field, array $raw, string $path): array
    {
        $result = [];
        foreach ($field->children as $childName => $child) {
            $result[$childName] = $this->submitField($form, $child, $raw[$childName] ?? null, $path . '.' . $childName);
        }

        $this->validationProcessor->applyConstraintErrors($form, $path, $result, $field->constraints);

        return $result;
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array<int, mixed>
     */
    private function submitCollection(Form $form, FieldConfig $field, array $raw, string $path): array
    {
        $items = [];
        foreach ($raw as $index => $row) {
            $items[] = $this->submitCollectionItem($form, $field, $row, $path, (string) $index);
        }

        if (($field->options['allow_delete'] ?? false) !== true) {
            $items = array_values($items);
        }

        $this->validationProcessor->applyConstraintErrors($form, $path, $items, $field->constraints);

        return $items;
    }

    private function submitCollectionItem(Form $form, FieldConfig $field, mixed $row, string $path, string $index): mixed
    {
        if (!is_array($row)) {
            $row = ['value' => $row];
        }

        $entryType = $field->entryType;
        if ($entryType !== null && is_subclass_of($entryType, FormTypeInterface::class)) {
            return $this->submitFormTypeCollectionEntry($form, $field, $row, $path, $index);
        }

        if ($entryType !== null && class_exists($entryType)) {
            /** @var array<int, DataTransformerInterface> $transformers */
            $transformers = method_exists($entryType, 'defaultTransformers') ? $entryType::defaultTransformers() : [];
            $entryField = new FieldConfig((string) $index, $entryType, $field->entryOptions, $field->constraints, $transformers);

            return $this->submitField($form, $entryField, $row, $path . '.' . $index);
        }

        return $row;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function submitFormTypeCollectionEntry(Form $form, FieldConfig $field, array $row, string $path, string $index): array
    {
        $builder = new FormBuilder($field->name . '_entry', null, $field->entryOptions + ['event_dispatcher' => $form->eventDispatcher()]);
        $entryTypeClass = (string) $field->entryType;
        $type = new $entryTypeClass();
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $resolved = $resolver->resolve($field->entryOptions);
        $type->buildForm($builder, $resolved);

        $entryValues = [];
        foreach ($builder->all() as $childName => $child) {
            $entryValues[$childName] = $this->submitField($form, $child, $row[$childName] ?? null, $path . '.' . $index . '.' . $childName);
        }

        return $entryValues;
    }

    /**
     * @param array<string, scalar|null> $parameters
     */
    private function trans(Form $form, string $key, string $fallback, array $parameters = []): string
    {
        $translator = $form->options()['translator'] ?? null;

        if ($translator instanceof TranslatorInterface) {
            return $translator->trans($key, $parameters);
        }

        $message = $fallback;
        foreach ($parameters as $name => $value) {
            $message = str_replace('{{' . $name . '}}', (string) $value, $message);
        }

        return $message;
    }
}

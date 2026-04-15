<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;

final class FormFieldSubmissionProcessor
{
    public function __construct(private readonly FormValidationProcessor $validationProcessor)
    {
    }

    public function submitField(Form $form, FieldConfig $field, mixed $raw, string $path): mixed
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
}

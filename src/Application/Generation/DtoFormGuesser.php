<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Generation;

/** @api */
final class DtoFormGuesser
{
    public function __construct(
        private readonly ?DtoAttributeReader $attributeReader = null,
    ) {
    }

    /**
     * @param mixed $source
     * @return array<string, mixed>
     */
    public function guess(mixed $source): array
    {
        if (is_array($source)) {
            return $this->guessFromArray($source);
        }

        if (!is_object($source)) {
            return [];
        }

        return $this->guessFromObject($source);
    }

    /**
     * @param array<string, mixed> $source
     * @return array<string, string>
     */
    private function guessFromArray(array $source): array
    {
        if ($source === []) {
            return [];
        }

        $fields = [];
        foreach ($source as $name => $value) {
            $fields[(string) $name] = $this->guessType($value);
        }

        ksort($fields);

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function guessFromObject(object $source): array
    {
        $data = get_object_vars($source);
        if ($data === []) {
            return [];
        }

        $attributes = $this->attributesFor($source);
        $hasRichMetadata = $attributes !== [];
        $fields = [];

        foreach ($data as $name => $value) {
            $key = (string) $name;

            if ($this->isIgnored($attributes, $key)) {
                continue;
            }

            $fields[$key] = $hasRichMetadata
                ? $this->richFieldDefinition($attributes, $key, $value)
                : $this->guessType($value);
        }

        ksort($fields);

        return $fields;
    }

    /**
     * @return array<string, array{type?: string, required?: bool, label?: string, ignored?: bool}>
     */
    private function attributesFor(object $source): array
    {
        return ($this->attributeReader ?? new DtoAttributeReader())->read($source);
    }

    /**
     * @param array<string, array{type?: string, required?: bool, label?: string, ignored?: bool}> $attributes
     */
    private function isIgnored(array $attributes, string $key): bool
    {
        return (($attributes[$key]['ignored'] ?? false) === true);
    }

    /**
     * @param array<string, array{type?: string, required?: bool, label?: string, ignored?: bool}> $attributes
     * @return array{type: string, required?: bool, label?: string}
     */
    private function richFieldDefinition(array $attributes, string $key, mixed $value): array
    {
        $attribute = $attributes[$key] ?? null;

        if (!is_array($attribute) || !isset($attribute['type'])) {
            return ['type' => $this->guessType($value)];
        }

        $field = ['type' => (string) $attribute['type']];

        if (isset($attribute['required'])) {
            $field['required'] = true;
        }

        if (array_key_exists('label', $attribute) && $attribute['label'] !== null) {
            $field['label'] = (string) $attribute['label'];
        }

        return $field;
    }

    private function guessType(mixed $value): string
    {
        return match (true) {
            is_null($value) => 'TextType',
            is_bool($value) => 'CheckboxType',
            is_int($value), is_float($value) => 'NumberType',
            is_array($value) => 'CollectionType',
            default => 'TextType',
        };
    }
}

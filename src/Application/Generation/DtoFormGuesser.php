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
     * @param object|array<string, mixed> $source
     * @return array<string, array{type: string, required?: bool, label?: string}>
     */
    public function guess(object|array $source): array
    {
        $data = is_array($source) ? $source : get_object_vars($source);

        if ($data === []) {
            return [];
        }

        $attributes = is_object($source)
            ? (($this->attributeReader ?? new DtoAttributeReader())->read($source))
            : [];

        $fields = [];

        foreach ($data as $name => $value) {
            $key = (string) $name;
            $attribute = $attributes[$key] ?? null;

            if (is_array($attribute) && (($attribute['ignored'] ?? false) === true)) {
                continue;
            }

            if (is_array($attribute) && isset($attribute['type'])) {
                $fields[$key] = ['type' => (string) $attribute['type']];

                if (isset($attribute['required'])) {
                    $fields[$key]['required'] = (bool) $attribute['required'];
                }

                if (array_key_exists('label', $attribute) && $attribute['label'] !== null) {
                    $fields[$key]['label'] = (string) $attribute['label'];
                }

                continue;
            }

            $fields[$key] = ['type' => $this->guessType($value)];
        }

        ksort($fields);

        return $fields;
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

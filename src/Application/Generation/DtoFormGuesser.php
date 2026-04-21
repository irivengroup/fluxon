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

        if (!is_object($source)) {
            return [];
        }

        $data = get_object_vars($source);

        if ($data === []) {
            return [];
        }

        $attributes = ($this->attributeReader ?? new DtoAttributeReader())->read($source);
        $hasRichMetadata = $attributes !== [];
        $fields = [];

        foreach ($data as $name => $value) {
            $key = (string) $name;
            $attribute = $attributes[$key] ?? null;

            if (is_array($attribute) && (($attribute['ignored'] ?? false) === true)) {
                continue;
            }

            if ($hasRichMetadata) {
                if (is_array($attribute) && isset($attribute['type'])) {
                    $field = ['type' => (string) $attribute['type']];

                    if (isset($attribute['required'])) {
                        $field['required'] = true;
                    }

                    if (array_key_exists('label', $attribute) && $attribute['label'] !== null) {
                        $field['label'] = (string) $attribute['label'];
                    }

                    $fields[$key] = $field;
                    continue;
                }

                $fields[$key] = ['type' => $this->guessType($value)];
                continue;
            }

            $fields[$key] = $this->guessType($value);
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

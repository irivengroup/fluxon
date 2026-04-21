<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Generation;

/** @api */
final class DtoFormGuesser
{
    /**
     * @return array<string, string>
     */
    public function guess(object|array $source): array
    {
        $data = is_array($source) ? $source : get_object_vars($source);
        $fields = [];

        foreach ($data as $name => $value) {
            $fields[(string) $name] = $this->guessType($value);
        }

        return $fields;
    }

    private function guessType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'CheckboxType',
            is_int($value), is_float($value) => 'NumberType',
            is_array($value) => 'CollectionType',
            default => 'TextType',
        };
    }
}

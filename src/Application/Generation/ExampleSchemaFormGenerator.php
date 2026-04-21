<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Generation;

/** @api */
final class ExampleSchemaFormGenerator
{
    public function __construct(
        private readonly DtoFormGuesser $guesser = new DtoFormGuesser(),
    ) {
    }

    /**
     * @param object|array<string, mixed> $sample
     * @return array{fields: array<string, array{type: string, required?: bool, label?: string}>}
     */
    public function generate(object|array $sample): array
    {
        $fields = $this->normalizeGuessedFields($this->guesser->guess($sample));
        ksort($fields);

        return ['fields' => $fields];
    }

    /**
     * @param array<string, mixed> $guessed
     * @return array<string, array{type: string, required?: bool, label?: string}>
     */
    private function normalizeGuessedFields(array $guessed): array
    {
        $fields = [];

        foreach ($guessed as $name => $definition) {
            $normalized = $this->normalizeDefinition($definition);
            if ($normalized === null) {
                continue;
            }

            $fields[(string) $name] = $normalized;
        }

        return $fields;
    }

    /**
     * @return array{type: string, required?: bool, label?: string}|null
     */
    private function normalizeDefinition(mixed $definition): ?array
    {
        if (is_string($definition)) {
            return ['type' => $definition];
        }

        if (!is_array($definition) || !isset($definition['type'])) {
            return null;
        }

        $field = ['type' => (string) $definition['type']];

        if (isset($definition['required'])) {
            $field['required'] = true;
        }

        if (array_key_exists('label', $definition) && $definition['label'] !== null) {
            $field['label'] = (string) $definition['label'];
        }

        return $field;
    }
}

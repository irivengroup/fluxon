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
        $guessed = $this->guesser->guess($sample);
        $fields = [];

        foreach ($guessed as $name => $definition) {
            if (is_string($definition)) {
                $fields[(string) $name] = ['type' => $definition];
                continue;
            }

            if (is_array($definition) && isset($definition['type'])) {
                $field = ['type' => (string) $definition['type']];

                if (isset($definition['required'])) {
                    $field['required'] = (bool) $definition['required'];
                }

                if (array_key_exists('label', $definition) && $definition['label'] !== null) {
                    $field['label'] = (string) $definition['label'];
                }

                $fields[(string) $name] = $field;
            }
        }

        ksort($fields);

        return ['fields' => $fields];
    }
}

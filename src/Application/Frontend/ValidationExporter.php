<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

use Iriven\PhpFormGenerator\Domain\Form\FieldConfig;

final class ValidationExporter
{
    /**
     * @return array<string, mixed>
     */
    public function export(FieldConfig $field): array
    {
        $rules = [
            'required' => (bool) ($field->options['required'] ?? false),
        ];

        foreach ($field->constraints as $constraint) {
            $class = strtolower($constraint::class);

            if (str_contains($class, 'length')) {
                $rules['type'] = 'length';
            }
            if (str_contains($class, 'choice')) {
                $rules['type'] = 'choice';
            }
            if (str_contains($class, 'count')) {
                $rules['type'] = 'count';
            }
        }

        return $rules;
    }
}

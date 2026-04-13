<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class ChoiceType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'choice';
    }

    public function transformToModel(mixed $value, array $options = []): mixed { return ($options['multiple'] ?? false) ? (is_array($value) ? array_values($value) : []) : $value; }
}
<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class NumberType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'number';
    }

    public function transformToModel(mixed $value, array $options = []): mixed { return $value === null || $value === '' ? null : (is_numeric($value) ? $value + 0 : $value); }
}
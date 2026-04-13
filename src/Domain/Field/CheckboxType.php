<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class CheckboxType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'checkbox';
    }

    public function transformToModel(mixed $value, array $options = []): mixed { return $value === '1' || $value === 1 || $value === true || $value === 'on'; }
}
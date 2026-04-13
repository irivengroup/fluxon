<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class NumberType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'number';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['min'] ??= '0';
        $options['attr']['max'] ??= '100';
        $options['attr']['step'] ??= '1';
        return $options;
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class MonthType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'month';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['pattern'] ??= '\\d{4}-\\d{2}';
        return $options;
    }
}

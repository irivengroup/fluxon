<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class DatalistType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'datalist';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['choices'] ??= [];
        return $options;
    }
}

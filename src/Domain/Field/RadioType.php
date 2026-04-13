<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class RadioType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'radio';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['choices'] ??= [];
        return $options;
    }
}

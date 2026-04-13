<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class ColorType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'color';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['title'] ??= '6-digit hexadecimal color (e.g. #000000)';
        $options['attr']['pattern'] ??= '#[a-fA-F0-9]{6}';
        return $options;
    }
}

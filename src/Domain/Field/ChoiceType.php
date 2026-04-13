<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class ChoiceType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'select';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['choices'] ??= [];
        $options['placeholder'] ??= null;
        $options['multiple'] ??= false;
        return $options;
    }
}

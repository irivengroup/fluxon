<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class YesNoType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'yes_no';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['choices'] = $defaults['choices'] ?: ['Yes' => '1', 'No' => '0'];
        return $defaults;
    }
}
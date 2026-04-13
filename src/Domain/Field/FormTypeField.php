<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class FormTypeField extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'form';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['form_type'] ??= null;
        return $defaults;
    }
}
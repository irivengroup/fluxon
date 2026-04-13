<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class CollectionType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'collection';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['entry_type'] ??= TextType::class;
        $defaults['entry_options'] ??= [];
        $defaults['allow_add'] ??= true;
        $defaults['allow_delete'] ??= true;
        $defaults['prototype'] ??= true;
        return $defaults;
    }
}
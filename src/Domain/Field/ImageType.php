<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class ImageType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'image';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['mapped'] ??= false;
        return $defaults;
    }
}
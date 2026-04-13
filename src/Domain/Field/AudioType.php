<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class AudioType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'audio';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['mapped'] ??= false;
        return $defaults;
    }
}
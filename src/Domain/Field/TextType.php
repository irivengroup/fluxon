<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class TextType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'text';
    }
}
<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class TelType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'tel';
    }
}
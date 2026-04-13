<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class DatetimeType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'datetime';
    }
}
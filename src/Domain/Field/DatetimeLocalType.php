<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class DatetimeLocalType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'datetime-local';
    }
}

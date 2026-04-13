<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class TimeType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'time';
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class RangeType extends NumberType
{
    public function renderType(): string
    {
        return 'range';
    }
}

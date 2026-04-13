<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class WeekType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'week';
    }
}

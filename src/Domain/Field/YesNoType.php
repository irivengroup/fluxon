<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class YesNoType extends SelectType
{
    /** @return array<string, string> */
    public static function choices(): array
    {
        return ['1' => 'Yes', '0' => 'No'];
    }
}

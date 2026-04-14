<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class HiddenType extends AbstractFieldType
{
    public static function htmlType(): string { return 'hidden'; }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class EmailType extends TextType
{
    public static function htmlType(): string { return 'email'; }
}

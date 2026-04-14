<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class CollectionType extends AbstractFieldType
{
    public static function htmlType(): string
    {
        return 'collection';
    }
}

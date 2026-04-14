<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Transformer\BooleanTransformer;

class CheckboxType extends AbstractFieldType
{
    public static function htmlType(): string
    {
        return 'checkbox';
    }

    public static function defaultTransformers(): array
    {
        return [new BooleanTransformer()];
    }
}

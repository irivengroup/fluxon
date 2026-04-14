<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Transformer\IntegerTransformer;

class IntegerType extends NumberType
{
    public static function defaultTransformers(): array
    {
        return [new IntegerTransformer()];
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Transformer\FloatTransformer;

class FloatType extends NumberType
{
    public static function defaultTransformers(): array
    {
        return [new FloatTransformer()];
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Transformer\BooleanTransformer;

class CheckboxType extends AbstractFieldType
{
    public static function htmlType(): string
    {
        return 'checkbox';
    }

    /** @return array<int, DataTransformerInterface> */
    public static function defaultTransformers(): array
    {
        return [new BooleanTransformer()];
    }
}

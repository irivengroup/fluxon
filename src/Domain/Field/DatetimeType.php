<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Transformer\DateTimeTransformer;

class DatetimeType extends AbstractFieldType
{
    public static function htmlType(): string
    {
        return 'datetime-local';
    }

    /** @return array<int, DataTransformerInterface> */
    public static function defaultTransformers(): array
    {
        return [new DateTimeTransformer()];
    }
}

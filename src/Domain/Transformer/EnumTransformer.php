<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Transformer;

use BackedEnum;
use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;

final class EnumTransformer implements DataTransformerInterface
{
    /**
     * @param string $enumClass
     */
    public function __construct(private readonly string $enumClass)
    {
    }

    public function transform(mixed $value): mixed
    {
        return $value instanceof BackedEnum ? $value->value : $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return $value;
        }

        if (!enum_exists($this->enumClass)) {
            throw new InvalidArgumentException('Enum class does not exist: ' . $this->enumClass);
        }

        return $this->enumClass::from($value);
    }
}

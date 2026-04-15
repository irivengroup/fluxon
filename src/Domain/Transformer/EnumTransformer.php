<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Transformer;

use BackedEnum;
use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use UnitEnum;

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
        if ($value instanceof BackedEnum) {
            /** @var object{value:int|string} $value */
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            /** @var object{name:string} $value */
            return $value->name;
        }

        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        if ($value instanceof UnitEnum) {
            return $value;
        }

        $this->assertEnumExists();

        return $this->isBackedEnum()
            ? $this->reverseTransformBackedEnum($value)
            : $this->reverseTransformUnitEnum($value);
    }

    private function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private function assertEnumExists(): void
    {
        if (!enum_exists($this->enumClass)) {
            throw new InvalidArgumentException('Enum class does not exist: ' . $this->enumClass);
        }
    }

    private function isBackedEnum(): bool
    {
        return is_subclass_of($this->enumClass, BackedEnum::class);
    }

    private function reverseTransformBackedEnum(mixed $value): BackedEnum
    {
        /** @var class-string<BackedEnum> $backedEnumClass */
        $backedEnumClass = $this->enumClass;

        return $backedEnumClass::from($value);
    }

    private function reverseTransformUnitEnum(mixed $value): UnitEnum
    {
        /** @var class-string<UnitEnum> $unitEnumClass */
        $unitEnumClass = $this->enumClass;
        $case = $this->findUnitEnumCaseByName($unitEnumClass, (string) $value);

        if ($case !== null) {
            return $case;
        }

        throw new InvalidArgumentException(sprintf(
            'Value "%s" is not a valid case name for enum %s.',
            (string) $value,
            $this->enumClass
        ));
    }

    /**
     * @param class-string<UnitEnum> $unitEnumClass
     */
    private function findUnitEnumCaseByName(string $unitEnumClass, string $name): ?UnitEnum
    {
        foreach ($unitEnumClass::cases() as $case) {
            /** @var object{name:string} $case */
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}

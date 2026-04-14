<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Type;

use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;

final class TypeResolver
{
    /**
     * @param string $typeClass
     * @return string
     */
    public static function resolveFieldType(string $typeClass): string
    {
        if (class_exists($typeClass) || interface_exists($typeClass)) {
            return $typeClass;
        }

        $shortName = self::shortName($typeClass);

        return BuiltinTypeRegistry::fieldTypes()[$shortName] ?? $typeClass;
    }

    /**
     * @param string $typeClass
     * @return string
     */
    public static function resolveFormType(string $typeClass): string
    {
        if (class_exists($typeClass) || interface_exists($typeClass)) {
            return $typeClass;
        }

        $shortName = self::shortName($typeClass);

        /** @var string $resolved */
        $resolved = BuiltinTypeRegistry::formTypes()[$shortName] ?? $typeClass;

        return $resolved;
    }

    private static function shortName(string $typeClass): string
    {
        $position = strrpos($typeClass, '\\');

        return $position === false ? $typeClass : substr($typeClass, $position + 1);
    }
}

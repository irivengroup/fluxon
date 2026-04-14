<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface FieldTypeExtensionInterface
{
    /**
     * @return string
     */
    public static function getExtendedType(): string;

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function extendOptions(array $options): array;

    /**
 * @param array<int, ConstraintInterface> $constraints
 * @param array<string, mixed> $options
 * @return array<int, ConstraintInterface>
 */
public function extendConstraints(array $constraints, array $options): array;

    /**
 * @param array<int, DataTransformerInterface> $transformers
 * @param array<string, mixed> $options
 * @return array<int, DataTransformerInterface>
 */
public function extendTransformers(array $transformers, array $options): array;
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;

abstract class AbstractFieldType implements FieldTypeInterface
{
    public function isCompound(): bool
    {
        return false;
    }

    public function isCollection(): bool
    {
        return false;
    }

    public function normalizeOptions(array $options): array
    {
        $options['attr'] ??= [];
        $options['constraints'] ??= [];
        return $options;
    }
}

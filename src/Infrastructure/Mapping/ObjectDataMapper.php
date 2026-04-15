<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

final class ObjectDataMapper
{
    /** @param array<string, mixed> $values */
    public function map(object $target, array $values): object
    {
        foreach ($values as $key => $value) {
            $this->assign($target, (string) $key, $value);
        }

        return $target;
    }

    private function assign(object $target, string $property, mixed $value): void
    {
        $setter = 'set' . ucfirst($property);
        $current = $this->readCurrentValue($target, $property);

        if ($this->shouldMapNestedObject($value, $current)) {
            $this->assignNestedObject($target, $property, $setter, $current, $value);

            return;
        }

        if ($this->shouldAssignArrayDirectly($value, $current)) {
            $target->{$property} = $value;

            return;
        }

        $this->writeValue($target, $property, $setter, $value);
    }

    private function readCurrentValue(object $target, string $property): mixed
    {
        $getter = 'get' . ucfirst($property);

        if (method_exists($target, $getter)) {
            return $target->{$getter}();
        }

        if (property_exists($target, $property)) {
            return $target->{$property};
        }

        return null;
    }

    private function shouldMapNestedObject(mixed $value, mixed $current): bool
    {
        return is_array($value) && is_object($current);
    }

    /**
     * @param array<string, mixed> $value
     */
    private function assignNestedObject(object $target, string $property, string $setter, object $current, array $value): void
    {
        foreach ($value as $childKey => $childValue) {
            $this->assign($current, (string) $childKey, $childValue);
        }

        $this->writeValue($target, $property, $setter, $current);
    }

    private function shouldAssignArrayDirectly(mixed $value, mixed $current): bool
    {
        return is_array($value) && is_array($current);
    }

    private function writeValue(object $target, string $property, string $setter, mixed $value): void
    {
        if (method_exists($target, $setter)) {
            $target->{$setter}($value);

            return;
        }

        if (property_exists($target, $property)) {
            $target->{$property} = $value;
        }
    }
}

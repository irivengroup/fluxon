<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

final class ObjectDataMapper
{
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
        $getter = 'get' . ucfirst($property);

        $current = null;
        if (method_exists($target, $getter)) {
            $current = $target->{$getter}();
        } elseif (property_exists($target, $property)) {
            $current = $target->{$property};
        }

        if (is_array($value) && is_object($current)) {
            foreach ($value as $childKey => $childValue) {
                $this->assign($current, (string) $childKey, $childValue);
            }
            if (method_exists($target, $setter)) {
                $target->{$setter}($current);
            } elseif (property_exists($target, $property)) {
                $target->{$property} = $current;
            }
            return;
        }

        if (is_array($value) && is_array($current)) {
            $target->{$property} = $value;
            return;
        }

        if (method_exists($target, $setter)) {
            $target->{$setter}($value);
            return;
        }

        if (property_exists($target, $property)) {
            $target->{$property} = $value;
        }
    }
}

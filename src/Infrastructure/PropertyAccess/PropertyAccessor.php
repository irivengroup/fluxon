<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\PropertyAccess;

use RuntimeException;

final class PropertyAccessor
{
    public function getValue(mixed $source, string $path, mixed $default = null): mixed
    {
        if ($path === '') {
            return $source;
        }

        $current = $source;
        foreach ($this->segments($path) as $segment) {
            if (!$this->canReadSegment($current, $segment)) {
                return $default;
            }

            $current = $this->readSegment($current, $segment, $default);
        }

        return $current;
    }

    public function setValue(mixed &$target, string $path, mixed $value): void
    {
        if ($path === '') {
            $target = $value;

            return;
        }

        $segments = $this->segments($path);
        $last = (string) array_pop($segments);
        $current =& $target;

        foreach ($segments as $segment) {
            $this->advanceWritableTarget($current, $segment, $path);
        }

        $this->writeFinalSegment($current, $last, $value, $path);
    }

    /** @return array<int,string> */
    private function segments(string $path): array
    {
        return explode('.', $path);
    }

    private function canReadSegment(mixed $current, string $segment): bool
    {
        if (is_array($current)) {
            return array_key_exists($segment, $current);
        }

        if (!is_object($current)) {
            return false;
        }

        return $this->hasObjectSegment($current, $segment);
    }

    private function readSegment(mixed $current, string $segment, mixed $default): mixed
    {
        if (is_array($current)) {
            return $current[$segment] ?? $default;
        }

        if (!is_object($current)) {
            return $default;
        }

        return $this->readObjectSegment($current, $segment, $default);
    }

    private function hasObjectSegment(object $current, string $segment): bool
    {
        return method_exists($current, $this->getterName($segment))
            || method_exists($current, $this->isserName($segment))
            || property_exists($current, $segment);
    }

    private function readObjectSegment(object $current, string $segment, mixed $default): mixed
    {
        $getter = $this->getterName($segment);
        $isser = $this->isserName($segment);

        if (method_exists($current, $getter)) {
            return $current->{$getter}();
        }

        if (method_exists($current, $isser)) {
            return $current->{$isser}();
        }

        if (property_exists($current, $segment)) {
            return $current->{$segment};
        }

        return $default;
    }

    private function advanceWritableTarget(mixed &$current, string $segment, string $path): void
    {
        if (is_array($current)) {
            $this->advanceArrayTarget($current, $segment);

            return;
        }

        if (is_object($current)) {
            $this->advanceObjectTarget($current, $segment);

            return;
        }

        throw new RuntimeException('Unable to navigate property path "' . $path . '".');
    }

    /**
     * @param array<string, mixed> $current
     */
    private function advanceArrayTarget(array &$current, string $segment): void
    {
        if (!isset($current[$segment]) || !$this->isNavigableValue($current[$segment])) {
            $current[$segment] = [];
        }

        $current =& $current[$segment];
    }

    private function advanceObjectTarget(object &$current, string $segment): void
    {
        if ($this->hasConcreteObjectProperty($current, $segment)) {
            $this->ensureNavigableObjectProperty($current, $segment);
            $current =& $current->{$segment};

            return;
        }

        $this->advanceObjectViaAccessor($current, $segment);
    }

    private function hasConcreteObjectProperty(object $current, string $segment): bool
    {
        return property_exists($current, $segment);
    }

    private function ensureNavigableObjectProperty(object $current, string $segment): void
    {
        if (!$this->isNavigableValue($current->{$segment})) {
            $current->{$segment} = [];
        }
    }

    private function advanceObjectViaAccessor(object &$current, string $segment): void
    {
        $getter = $this->getterName($segment);

        if (!method_exists($current, $getter)) {
            $current->{$segment} = [];
            $current =& $current->{$segment};

            return;
        }

        $this->ensureAccessorTargetIsNavigable($current, $segment, $getter);
        $current->{$segment} = $current->{$getter}();
        $current =& $current->{$segment};
    }

    private function ensureAccessorTargetIsNavigable(object $current, string $segment, string $getter): void
    {
        $child = $current->{$getter}();
        if ($this->isNavigableValue($child)) {
            return;
        }

        $child = [];
        $setter = $this->setterName($segment);

        if (method_exists($current, $setter)) {
            $current->{$setter}($child);

            return;
        }

        $current->{$segment} = $child;
    }

    private function writeFinalSegment(mixed &$current, string $last, mixed $value, string $path): void
    {
        if (is_array($current)) {
            $current[$last] = $value;

            return;
        }

        if (is_object($current)) {
            $setter = $this->setterName($last);

            if (method_exists($current, $setter)) {
                $current->{$setter}($value);

                return;
            }

            $current->{$last} = $value;

            return;
        }

        throw new RuntimeException('Unable to write property path "' . $path . '".');
    }

    private function getterName(string $segment): string
    {
        return 'get' . ucfirst($segment);
    }

    private function isserName(string $segment): string
    {
        return 'is' . ucfirst($segment);
    }

    private function setterName(string $segment): string
    {
        return 'set' . ucfirst($segment);
    }

    private function isNavigableValue(mixed $value): bool
    {
        return is_array($value) || is_object($value);
    }
}

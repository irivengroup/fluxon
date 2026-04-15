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

        $segments = explode('.', $path);
        $current = $source;

        foreach ($segments as $segment) {
            if (is_array($current)) {
                if (!array_key_exists($segment, $current)) {
                    return $default;
                }
                $current = $current[$segment];
                continue;
            }

            if (!is_object($current)) {
                return $default;
            }

            $getter = 'get' . ucfirst($segment);
            $isser = 'is' . ucfirst($segment);

            if (method_exists($current, $getter)) {
                $current = $current->{$getter}();
                continue;
            }

            if (method_exists($current, $isser)) {
                $current = $current->{$isser}();
                continue;
            }

            if (property_exists($current, $segment)) {
                $current = $current->{$segment};
                continue;
            }

            return $default;
        }

        return $current;
    }

    public function setValue(mixed &$target, string $path, mixed $value): void
    {
        if ($path === '') {
            $target = $value;

            return;
        }

        $segments = explode('.', $path);
        $last = (string) array_pop($segments);
        $current =& $target;

        foreach ($segments as $segment) {
            $this->advanceWritableTarget($current, $segment, $path);
        }

        $this->writeFinalSegment($current, $last, $value, $path);
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
        if (property_exists($current, $segment)) {
            if (!$this->isNavigableValue($current->{$segment})) {
                $current->{$segment} = [];
            }
            $current =& $current->{$segment};

            return;
        }

        $getter = 'get' . ucfirst($segment);
        $setter = 'set' . ucfirst($segment);

        if (method_exists($current, $getter)) {
            $child = $current->{$getter}();

            if (!$this->isNavigableValue($child)) {
                $child = [];
                if (method_exists($current, $setter)) {
                    $current->{$setter}($child);
                } else {
                    $current->{$segment} = $child;
                }
            }

            if (!property_exists($current, $segment)) {
                $current->{$segment} = method_exists($current, $getter) ? $current->{$getter}() : $child;
            }

            $current =& $current->{$segment};

            return;
        }

        $current->{$segment} = [];
        $current =& $current->{$segment};
    }

    private function writeFinalSegment(mixed &$current, string $last, mixed $value, string $path): void
    {
        if (is_array($current)) {
            $current[$last] = $value;

            return;
        }

        if (is_object($current)) {
            $setter = 'set' . ucfirst($last);
            if (method_exists($current, $setter)) {
                $current->{$setter}($value);

                return;
            }

            $current->{$last} = $value;

            return;
        }

        throw new RuntimeException('Unable to write property path "' . $path . '".');
    }

    private function isNavigableValue(mixed $value): bool
    {
        return is_array($value) || is_object($value);
    }
}

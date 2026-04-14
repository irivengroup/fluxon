<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\PropertyAccess;

use ReflectionClass;
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
        $segments = explode('.', $path);
        $last = array_pop($segments);

        if ($last === null) {
            $target = $value;
            return;
        }

        $current =& $target;

        foreach ($segments as $segment) {
            if (is_array($current)) {
                if (!isset($current[$segment]) || (!is_array($current[$segment]) && !is_object($current[$segment]))) {
                    $current[$segment] = [];
                }
                $current =& $current[$segment];
                continue;
            }

            if (is_object($current)) {
                if (property_exists($current, $segment)) {
                    if (!is_array($current->{$segment}) && !is_object($current->{$segment})) {
                        $current->{$segment} = [];
                    }
                    $current =& $current->{$segment};
                    continue;
                }

                $setter = 'set' . ucfirst($segment);
                $getter = 'get' . ucfirst($segment);
                if (method_exists($current, $getter)) {
                    $child = $current->{$getter}();
                    if (!is_array($child) && !is_object($child)) {
                        $child = [];
                        if (method_exists($current, $setter)) {
                            $current->{$setter}($child);
                        } else {
                            $current->{$segment} = $child;
                        }
                    }
                    if (method_exists($current, $getter)) {
                        $tmp = $current->{$getter}();
                        $current =& $tmp; // not by reference, handled below by final write
                    }
                }

                if (!property_exists($current, $segment)) {
                    $current->{$segment} = [];
                }
                $current =& $current->{$segment};
                continue;
            }

            throw new RuntimeException('Unable to navigate property path "' . $path . '".');
        }

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
}

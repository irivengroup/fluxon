<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Options;

use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Contract\OptionsResolverInterface;

final class OptionsResolver implements OptionsResolverInterface
{
    /** @var array<string, mixed> */
    private array $defaults = [];

    /** @var array<int, string> */
    private array $required = [];

    /** @var array<string, array<int, string>> */
    private array $allowedTypes = [];

    /** @var array<string, callable|array<int, mixed>> */
    private array $allowedValues = [];

    /** @param array<string, mixed> $defaults */
    public function setDefaults(array $defaults): self
    {
        $this->defaults = $defaults + $this->defaults;

        return $this;
    }

    /** @param array<int, string> $required */
    public function setRequired(array $required): self
    {
        $this->required = array_values(array_unique(array_merge($this->required, $required)));

        return $this;
    }

    /** @param string|array<int, string> $types */
    public function setAllowedTypes(string $option, string|array $types): self
    {
        $this->allowedTypes[$option] = array_values((array) $types);

        return $this;
    }

    /** @param callable|array<int, mixed> $values */
    public function setAllowedValues(string $option, callable|array $values): self
    {
        $this->allowedValues[$option] = $values;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function resolve(array $options = []): array
    {
        $resolved = $this->mergeDefaultsWithOptions($options);
        $this->assertRequiredOptions($resolved);
        $this->assertAllowedTypes($resolved);
        $this->assertAllowedValues($resolved);

        return $resolved;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function mergeDefaultsWithOptions(array $options): array
    {
        $resolved = $this->defaults;
        foreach ($options as $name => $value) {
            $resolved[$name] = $value;
        }

        return $resolved;
    }

    /** @param array<string, mixed> $resolved */
    private function assertRequiredOptions(array $resolved): void
    {
        foreach ($this->required as $name) {
            if (!array_key_exists($name, $resolved)) {
                throw new InvalidArgumentException(sprintf('The required option "%s" is missing.', $name));
            }
        }
    }

    /** @param array<string, mixed> $resolved */
    private function assertAllowedTypes(array $resolved): void
    {
        foreach ($this->allowedTypes as $name => $types) {
            if (!array_key_exists($name, $resolved) || $resolved[$name] === null) {
                continue;
            }

            if (!$this->matchesAllowedTypes($resolved[$name], $types)) {
                throw new InvalidArgumentException(sprintf('The option "%s" must be of type %s.', $name, implode('|', $types)));
            }
        }
    }

    /** @param array<string, mixed> $resolved */
    private function assertAllowedValues(array $resolved): void
    {
        foreach ($this->allowedValues as $name => $allowed) {
            if (!array_key_exists($name, $resolved)) {
                continue;
            }

            if (!$this->matchesAllowedValue($resolved[$name], $allowed)) {
                throw new InvalidArgumentException(sprintf('The option "%s" has an invalid value.', $name));
            }
        }
    }

    /** @param array<int, string> $types */
    private function matchesAllowedTypes(mixed $value, array $types): bool
    {
        foreach ($types as $type) {
            if ($this->matchesBuiltInType($value, $type)) {
                return true;
            }

            if ($this->matchesClassOrInterfaceType($value, $type)) {
                return true;
            }
        }

        return false;
    }

    private function matchesBuiltInType(mixed $value, string $type): bool
    {
        return match ($type) {
            'array' => is_array($value),
            'bool' => is_bool($value),
            'int' => is_int($value),
            'float' => is_float($value),
            'numeric' => is_numeric($value),
            'string' => is_string($value),
            'callable' => is_callable($value),
            'null', 'NULL' => $value === null,
            default => false,
        };
    }

    private function matchesClassOrInterfaceType(mixed $value, string $type): bool
    {
        return (class_exists($type) || interface_exists($type)) && $value instanceof $type;
    }

    /** @param callable|array<int, mixed> $allowed */
    private function matchesAllowedValue(mixed $value, callable|array $allowed): bool
    {
        if (is_callable($allowed)) {
            return $allowed($value);
        }

        return in_array($value, $allowed, true);
    }
}

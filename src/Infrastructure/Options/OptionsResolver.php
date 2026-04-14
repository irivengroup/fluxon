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
        $resolved = $this->defaults;
        foreach ($options as $name => $value) {
            $resolved[$name] = $value;
        }

        foreach ($this->required as $name) {
            if (!array_key_exists($name, $resolved)) {
                throw new InvalidArgumentException(sprintf('The required option "%s" is missing.', $name));
            }
        }

        foreach ($this->allowedTypes as $name => $types) {
            if (!array_key_exists($name, $resolved) || $resolved[$name] === null) {
                continue;
            }

            if (!$this->matchesAllowedTypes($resolved[$name], $types)) {
                throw new InvalidArgumentException(sprintf('The option "%s" must be of type %s.', $name, implode('|', $types)));
            }
        }

        foreach ($this->allowedValues as $name => $allowed) {
            if (!array_key_exists($name, $resolved)) {
                continue;
            }

            $value = $resolved[$name];
            if (is_callable($allowed)) {
                if (!$allowed($value)) {
                    throw new InvalidArgumentException(sprintf('The option "%s" has an invalid value.', $name));
                }
                continue;
            }

            if (!in_array($value, $allowed, true)) {
                throw new InvalidArgumentException(sprintf('The option "%s" has an invalid value.', $name));
            }
        }

        return $resolved;
    }

    /** @param array<int, string> $types */
    private function matchesAllowedTypes(mixed $value, array $types): bool
    {
        foreach ($types as $type) {
            if ($type === 'array' && is_array($value)) { return true; }
            if ($type === 'bool' && is_bool($value)) { return true; }
            if ($type === 'int' && is_int($value)) { return true; }
            if ($type === 'float' && is_float($value)) { return true; }
            if ($type === 'numeric' && is_numeric($value)) { return true; }
            if ($type === 'string' && is_string($value)) { return true; }
            if ($type === 'callable' && is_callable($value)) { return true; }
            if (($type === 'null' || $type === 'NULL') && $value === null) { return true; }
            if ((class_exists($type) || interface_exists($type)) && $value instanceof $type) { return true; }
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\ValueObject;

final class Attributes
{
    private array $attributes = [];
    private array $ignored = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$this->normalize((string) $key)] = $value;
        }
    }

    public function set(string $key, mixed $value): self
    {
        $this->attributes[$this->normalize($key)] = $value;
        return $this;
    }

    public function add(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->set((string) $key, $value);
        }
        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$this->normalize($key)] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($this->normalize($key), $this->attributes);
    }

    public function remove(string $key): self
    {
        unset($this->attributes[$this->normalize($key)]);
        return $this;
    }

    public function ignore(string|array $keys): self
    {
        foreach ((array) $keys as $key) {
            $this->ignored[] = $this->normalize((string) $key);
        }
        $this->ignored = array_values(array_unique($this->ignored));
        return $this;
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function render(): string
    {
        $parts = [];
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->ignored, true) || $value === null || $value === false) {
                continue;
            }
            if (is_int($key)) {
                $parts[] = $this->escape((string) $value);
                continue;
            }
            if ($value === true) {
                $parts[] = $key;
                continue;
            }
            if (is_array($value)) {
                $value = implode(' ', array_map(static fn (mixed $item): string => (string) $item, $value));
            }
            $parts[] = sprintf('%s="%s"', $key, $this->escape((string) $value));
        }
        return $parts === [] ? '' : ' ' . implode(' ', $parts);
    }

    public function createElementId(string $key): self
    {
        $key = ucfirst($this->normalize($key));
        if (!str_starts_with($key, 'input')) {
            $key = 'input' . $key;
        }
        return $this->set('id', $key);
    }

    private function normalize(string $key): string
    {
        $key = strtolower(trim($key));
        $key = preg_replace('/[^a-z0-9\-_]+/', '-', $key) ?? $key;
        return trim($key, '-');
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class HtmlAttributes
{
    private array $attributes = [];
    private array $ignored = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->set((string) $key, $value);
        }
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $key = Str::normalizeKey($key);

        return $this->attributes[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists(Str::normalizeKey($key), $this->attributes);
    }

    public function set(string $key, mixed $value): self
    {
        $this->attributes[Str::normalizeKey($key)] = $value;

        return $this;
    }

    public function add(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->set((string) $key, $value);
        }

        return $this;
    }

    public function remove(string $key): self
    {
        unset($this->attributes[Str::normalizeKey($key)]);

        return $this;
    }

    public function ignore(string|array $keys): self
    {
        foreach ((array) $keys as $key) {
            $this->ignored[Str::normalizeKey((string) $key)] = true;
        }

        return $this;
    }

    public function createElementId(string $name): self
    {
        return $this->set('id', Str::elementIdFromName($name));
    }

    public function render(string $type): string
    {
        $pairs = [];
        foreach ($this->attributes as $key => $value) {
            if (isset($this->ignored[$key])) {
                continue;
            }
            if (!AttributePolicy::allows($type, $key)) {
                continue;
            }
            if ($value === null || $value === false) {
                continue;
            }

            if (is_int($key)) {
                $pairs[] = Escaper::attr($value);
                continue;
            }

            if ($value === true) {
                $pairs[] = sprintf('%s="%s"', $key, $key);
                continue;
            }

            if (is_array($value)) {
                $value = implode(' ', array_map(static fn (mixed $item): string => (string) $item, $value));
            }

            $pairs[] = sprintf('%s="%s"', $key, Escaper::attr($value));
        }

        return $pairs ? ' ' . implode(' ', $pairs) : '';
    }
}

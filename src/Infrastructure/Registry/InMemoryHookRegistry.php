<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Registry;

use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;

final class InMemoryHookRegistry
{
    /** @var array<string, array<int, FormHookInterface>> */
    private array $hooks = [];

    public function register(FormHookInterface $hook): void
    {
        $name = $this->normalizeName($hook::getName());
        $this->hooks[$name] ??= [];
        $this->hooks[$name][] = $hook;
    }

    /**
     * @return array<int, FormHookInterface>
     */
    public function for(string $name): array
    {
        return $this->hooks[$this->normalizeName($name)] ?? [];
    }

    /**
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_keys($this->hooks);
    }

    /**
     * @return array<string, array<int, FormHookInterface>>
     */
    public function all(): array
    {
        return $this->hooks;
    }

    private function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));

        if ($name === '') {
            throw new InvalidArgumentException('Hook name cannot be empty.');
        }

        return $name;
    }
}

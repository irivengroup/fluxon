<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface RequestInterface
{
    public function method(): string;
    public function input(string $key, mixed $default = null): mixed;
    public function has(string $key): bool;
    /** @return array<string,mixed> */
    public function all(): array;
    /** @return array<string,mixed> */
    public function files(): array;
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Http;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;

final class ArrayRequest implements RequestInterface
{
    /** @param array<string,mixed> $input */
    public function __construct(
        private readonly string $method = 'POST',
        private readonly array $input = [],
        private readonly array $files = [],
    ) {
    }

    public function method(): string
    {
        return $this->method;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->input);
    }

    public function all(): array
    {
        return $this->input;
    }

    public function files(): array
    {
        return $this->files;
    }
}

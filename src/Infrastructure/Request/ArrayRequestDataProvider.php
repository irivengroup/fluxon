<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Request;

final class ArrayRequestDataProvider implements RequestDataProviderInterface
{
    public function __construct(
        private readonly string $method = 'GET',
        private readonly array $query = [],
        private readonly array $post = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            query: $_GET ?? [],
            post: $_POST ?? [],
        );
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        $source = match ($this->getMethod()) {
            'POST', 'PUT', 'PATCH', 'DELETE' => $this->post,
            default => $this->query,
        };

        return $source[$name] ?? $default;
    }
}

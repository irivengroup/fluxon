<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure;

final class ArrayRequestDataProvider implements RequestDataProviderInterface
{
    public function __construct(
        private readonly array $query = [],
        private readonly array $request = [],
        private readonly string $method = 'GET',
    ) {}

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function value(string $name, mixed $default = null): mixed
    {
        $source = in_array($this->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) ? $this->request : $this->query;
        return $source[$name] ?? $default;
    }
}

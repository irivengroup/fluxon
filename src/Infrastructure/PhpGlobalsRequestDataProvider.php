<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure;

final class PhpGlobalsRequestDataProvider implements RequestDataProviderInterface
{
    public function method(): string
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    public function value(string $name, mixed $default = null): mixed
    {
        $source = in_array($this->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) ? $_POST : $_GET;
        return $source[$name] ?? $default;
    }
}

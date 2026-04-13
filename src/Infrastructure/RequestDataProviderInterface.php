<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure;

interface RequestDataProviderInterface
{
    public function method(): string;
    public function value(string $name, mixed $default = null): mixed;
}

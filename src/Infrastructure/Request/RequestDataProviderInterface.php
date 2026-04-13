<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Request;

interface RequestDataProviderInterface
{
    public function getMethod(): string;

    public function get(string $name, mixed $default = null): mixed;
}

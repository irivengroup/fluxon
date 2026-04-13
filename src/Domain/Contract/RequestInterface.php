<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface RequestInterface
{
    public function all(): array;
    public function getMethod(): string;
    public function getFormData(string $formName): array;
}

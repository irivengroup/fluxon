<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Http;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;

final class ArrayRequest implements RequestInterface
{
    public function __construct(
        private readonly array $data = [],
        private readonly string $method = 'POST',
    ) {
    }

    public function all(): array
    {
        return $this->data;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getFormData(string $formName): array
    {
        $value = $this->data[$formName] ?? [];
        return is_array($value) ? $value : [];
    }
}

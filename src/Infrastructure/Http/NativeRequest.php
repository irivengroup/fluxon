<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Http;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;

final class NativeRequest implements RequestInterface
{
    public function __construct(
        private readonly array $post = [],
        private readonly array $get = [],
        private readonly string $method = 'POST',
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self($_POST, $_GET, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function all(): array
    {
        return $this->getMethod() === 'GET' ? $this->get : $this->post;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getFormData(string $formName): array
    {
        $source = $this->all();
        $value = $source[$formName] ?? [];
        return is_array($value) ? $value : [];
    }
}

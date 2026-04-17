<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Runtime;

final class RuntimePayload
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly ?string $theme = null,
        private readonly ?string $renderer = null,
        private readonly array $metadata = [],
    ) {
    }

    public function theme(): ?string
    {
        return $this->theme;
    }

    public function renderer(): ?string
    {
        return $this->renderer;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function metadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function withMetadata(array $metadata): self
    {
        return new self($this->theme, $this->renderer, $metadata);
    }
}

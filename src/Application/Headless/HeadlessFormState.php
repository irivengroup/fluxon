<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Headless;

/**
 * @api
 */
final class HeadlessFormState
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $errors
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly bool $submitted,
        private readonly bool $valid,
        private readonly array $payload = [],
        private readonly array $errors = [],
        private readonly array $metadata = [],
    ) {
    }

    public function submitted(): bool
    {
        return $this->submitted;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }
}

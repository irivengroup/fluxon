<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Observability;

/** @api */
final class StructuredLogger
{
    /** @var array<int, array<string, mixed>> */
    private array $entries = [];

    /**
     * @param array<string, mixed> $context
     */
    public function log(string $message, array $context = []): void
    {
        $this->entries[] = ['message' => $message, 'context' => $context];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function entries(): array
    {
        return $this->entries;
    }

    public function count(): int
    {
        return count($this->entries);
    }
}

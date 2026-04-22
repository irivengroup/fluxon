<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Runtime;

/** @api */
final class QueueTransport implements TransportInterface
{
    /** @var array<int, array<string, mixed>> */
    private array $queue = [];

    private bool $available = true;

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function send(array $payload): array
    {
        if (!$this->available) {
            return [
                'transport' => 'queue',
                'status' => 'unavailable',
                'payload' => $payload,
                'queue_size' => count($this->queue),
            ];
        }

        $this->queue[] = $payload;

        return [
            'transport' => 'queue',
            'status' => 'queued',
            'payload' => $payload,
            'queue_size' => count($this->queue),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function queued(): array
    {
        return $this->queue;
    }

    public function size(): int
    {
        return count($this->queue);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Observability;

/** @api */
final class InMemoryMetricsCollector
{
    /** @var array<string, float|int|bool|string> */
    private array $values = [];

    /**
     * @param float|int|bool|string $value
     */
    public function record(string $key, float|int|bool|string $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @return array<string, float|int|bool|string>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }
}

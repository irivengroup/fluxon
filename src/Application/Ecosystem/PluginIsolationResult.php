<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Ecosystem;

final class PluginIsolationResult
{
    public function __construct(
        private readonly string $plugin,
        private readonly bool $success,
        private readonly ?string $error = null,
    ) {
    }

    public function plugin(): string
    {
        return $this->plugin;
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'plugin' => $this->plugin,
            'success' => $this->success,
            'error' => $this->error,
        ];
    }
}

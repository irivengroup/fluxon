<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Plugins;

final class PluginContext
{
    /** @var array<string, mixed> */
    private array $config;

    /** @var array<int, string> */
    private array $logs = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return $this->config;
    }

    public function log(string $message): void
    {
        $this->logs[] = $message;
    }

    /**
     * @return array<int, string>
     */
    public function logs(): array
    {
        return $this->logs;
    }
}

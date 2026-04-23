<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Ecosystem;

use Iriven\Fluxon\Application\Plugins\OfficialPluginInterface;
use Iriven\Fluxon\Application\Plugins\PluginContext;

final class PluginCatalog
{
    /** @var array<int, OfficialPluginInterface> */
    private array $plugins = [];

    public function register(OfficialPluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * @return array<int, OfficialPluginInterface>
     */
    public function all(): array
    {
        return $this->plugins;
    }

    /**
     * @return array<int, array{name:string,version:string}>
     */
    public function manifest(): array
    {
        $items = [];
        foreach ($this->plugins as $plugin) {
            $items[] = [
                'name' => $plugin->name(),
                'version' => $plugin->version(),
            ];
        }

        return $items;
    }

    /**
     * @param PluginContext|null $context
     * @return array<int, PluginIsolationResult>
     */
    public function boot(?PluginContext $context = null): array
    {
        $context = $context ?? new PluginContext();
        $results = [];

        foreach ($this->plugins as $plugin) {
            try {
                $plugin->register($context);
                $results[] = new PluginIsolationResult($plugin->name(), true);
            } catch (\Throwable $e) {
                $results[] = new PluginIsolationResult($plugin->name(), false, $e->getMessage());
            }
        }

        return $results;
    }
}

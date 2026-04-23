<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Cli;

use Iriven\Fluxon\Application\Ecosystem\PluginCatalog;
use Iriven\Fluxon\Application\Plugins\CsrfPlugin;
use Iriven\Fluxon\Application\Plugins\EditorPlugin;

final class PluginListCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'plugin:list';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $catalog = new PluginCatalog();
        $catalog->register(new CsrfPlugin());
        $catalog->register(new EditorPlugin());

        return json_encode($catalog->manifest(), JSON_PRETTY_PRINT) ?: '[]';
    }
}

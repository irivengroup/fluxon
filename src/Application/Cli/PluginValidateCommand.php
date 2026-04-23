<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Cli;

use Iriven\Fluxon\Application\Ecosystem\PluginCatalog;
use Iriven\Fluxon\Application\Plugins\CsrfPlugin;
use Iriven\Fluxon\Application\Plugins\EditorPlugin;

final class PluginValidateCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'plugin:validate';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $catalog = new PluginCatalog();
        $catalog->register(new CsrfPlugin());
        $catalog->register(new EditorPlugin());

        $results = [];
        foreach ($catalog->boot() as $result) {
            $results[] = $result->toArray();
        }

        return json_encode($results, JSON_PRETTY_PRINT) ?: '[]';
    }
}

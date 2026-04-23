<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Cli;

use Iriven\Fluxon\Application\Ecosystem\CompatibilityMatrix;
use Iriven\Fluxon\Application\Ecosystem\PluginCatalog;
use Iriven\Fluxon\Application\Plugins\CsrfPlugin;
use Iriven\Fluxon\Application\Plugins\EditorPlugin;
use Iriven\Fluxon\Application\Sdk\SdkCatalog;

final class EcosystemReportCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'ecosystem:report';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $catalog = new PluginCatalog();
        $catalog->register(new CsrfPlugin());
        $catalog->register(new EditorPlugin());

        return json_encode([
            'plugins' => $catalog->manifest(),
            'sdks' => (new SdkCatalog())->all(),
            'compatibility' => (new CompatibilityMatrix())->report(),
        ], JSON_PRETTY_PRINT) ?: '{}';
    }
}

<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Plugins;

final class CsrfPlugin implements OfficialPluginInterface
{
    public function name(): string
    {
        return 'csrf';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function register(PluginContext $context): void
    {
        $context->log('csrf plugin registered');
    }
}

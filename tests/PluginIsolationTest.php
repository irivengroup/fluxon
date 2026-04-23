<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Ecosystem\PluginCatalog;
use Iriven\Fluxon\Application\Plugins\OfficialPluginInterface;
use Iriven\Fluxon\Application\Plugins\PluginContext;
use PHPUnit\Framework\TestCase;

final class PluginIsolationTest extends TestCase
{
    public function testFaultyPluginIsIsolated(): void
    {
        $catalog = new PluginCatalog();
        $catalog->register(new class implements OfficialPluginInterface {
            public function name(): string { return 'broken'; }
            public function version(): string { return '1.0.0'; }
            public function register(PluginContext $context): void { throw new \RuntimeException('broken'); }
        });

        $results = $catalog->boot();

        self::assertFalse($results[0]->success());
        self::assertSame('broken', $results[0]->plugin());
    }
}

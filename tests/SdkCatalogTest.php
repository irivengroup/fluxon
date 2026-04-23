<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Sdk\SdkCatalog;
use PHPUnit\Framework\TestCase;

final class SdkCatalogTest extends TestCase
{
    public function testSdkCatalogIsStructured(): void
    {
        $items = (new SdkCatalog())->all();

        self::assertContains('php', $items);
        self::assertContains('javascript-manifest', $items);
    }
}

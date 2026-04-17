<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\DemoPlugin;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\EmptyPlugin;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\OverridePlugin;
use PHPUnit\Framework\TestCase;

final class PluginInterfaceSignatureRegressionTest extends TestCase
{
    public function testProjectPluginFixturesImplementPluginInterface(): void
    {
        self::assertInstanceOf(PluginInterface::class, new DemoPlugin());
        self::assertInstanceOf(PluginInterface::class, new EmptyPlugin());
        self::assertInstanceOf(PluginInterface::class, new OverridePlugin());
    }
}

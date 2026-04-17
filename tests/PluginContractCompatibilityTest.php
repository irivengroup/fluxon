<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\DemoPlugin;
use PHPUnit\Framework\TestCase;

final class PluginContractCompatibilityTest extends TestCase
{
    public function testLegacyPluginContractRemainsCompatibleWithRegistry(): void
    {
        $registry = new PluginRegistry();
        $registry->add(new DemoPlugin());

        self::assertIsArray($registry->fieldTypes());
        self::assertIsArray($registry->formTypes());
        self::assertIsArray($registry->extensions());
    }
}

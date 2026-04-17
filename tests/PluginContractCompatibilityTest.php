<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFieldTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFormTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\DemoPlugin;
use PHPUnit\Framework\TestCase;

final class PluginContractCompatibilityTest extends TestCase
{
    public function testLegacyPluginContractRemainsCompatibleWithRegistry(): void
    {
        $registry = new PluginRegistry(
            new InMemoryFieldTypeRegistry(),
            new InMemoryFormTypeRegistry(),
            new ExtensionRegistry(),
        );

        $registry->registerPlugin(new DemoPlugin());

        self::assertNotEmpty($registry->all());
    }
}

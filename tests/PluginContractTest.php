<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Domain\Contract\FieldExtensionInterface;
use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use PHPUnit\Framework\TestCase;

final class PluginContractTest extends TestCase
{
    public function testPluginContractsExist(): void
    {
        self::assertTrue(interface_exists(PluginInterface::class));
        self::assertTrue(interface_exists(FieldExtensionInterface::class));
    }
}

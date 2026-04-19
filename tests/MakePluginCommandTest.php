<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Cli\MakePluginCommand;
use PHPUnit\Framework\TestCase;

final class MakePluginCommandTest extends TestCase
{
    public function testMakePluginScaffoldsPluginContract(): void
    {
        $output = (new MakePluginCommand())->run(['AcmePlugin']);

        self::assertStringContainsString('final class AcmePlugin implements PluginInterface', $output);
        self::assertStringContainsString('registerFieldTypes', $output);
        self::assertStringContainsString('registerFormTypes', $output);
        self::assertStringContainsString('registerExtensions', $output);
    }
}

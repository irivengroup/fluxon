<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormPluginKernel;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\DemoPlugin;
use PHPUnit\Framework\TestCase;

final class PluginKernelHardeningTest extends TestCase
{
    public function testPluginRegistrationStillWorksWithValidatorEnabled(): void
    {
        $kernel = new FormPluginKernel();
        $kernel->register(new DemoPlugin());

        self::assertCount(1, $kernel->plugins()->all());
    }
}

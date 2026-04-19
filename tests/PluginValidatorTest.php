<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Plugin\PluginValidator;
use Iriven\PhpFormGenerator\Tests\Fixtures\Plugin\DemoPlugin;
use PHPUnit\Framework\TestCase;

final class PluginValidatorTest extends TestCase
{
    public function testProjectPluginFixturePassesValidation(): void
    {
        (new PluginValidator())->validate(new DemoPlugin());
        self::assertTrue(true);
    }
}

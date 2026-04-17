<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\FrontendSdkConfig;
use PHPUnit\Framework\TestCase;

final class SchemaVersionCompatibilityTest extends TestCase
{
    public function testSchemaVersionIsFrozenToV2ForPublicApi(): void
    {
        self::assertSame('2.0', (new FrontendSdkConfig())->schemaVersion());
    }
}

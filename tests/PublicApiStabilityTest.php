<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\FrontendSdk;
use Iriven\PhpFormGenerator\Application\Frontend\FrontendSdkConfig;
use Iriven\PhpFormGenerator\Application\FormRuntimeContext;
use Iriven\PhpFormGenerator\Application\FormRuntimePipeline;
use PHPUnit\Framework\TestCase;

final class PublicApiStabilityTest extends TestCase
{
    public function testSdkAndRuntimeContractsRemainInstantiable(): void
    {
        self::assertInstanceOf(FrontendSdkConfig::class, new FrontendSdkConfig());
        self::assertTrue(method_exists(FrontendSdk::class, 'getSchemaVersion'));
        self::assertTrue(method_exists(FrontendSdk::class, 'getFramework'));
        self::assertTrue(class_exists(FormRuntimeContext::class));
        self::assertTrue(class_exists(FormRuntimePipeline::class));
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Runtime\RuntimePayload;
use PHPUnit\Framework\TestCase;

final class RuntimePayloadTest extends TestCase
{
    public function testRuntimePayloadExposesTypedAccessors(): void
    {
        $payload = new RuntimePayload('tailwind', 'RendererClass', ['variant' => 'compact', 'debug' => true]);

        self::assertSame('tailwind', $payload->theme());
        self::assertSame('RendererClass', $payload->renderer());
        self::assertSame('compact', $payload->metadataValue('variant'));
        self::assertTrue($payload->metadataValue('debug'));
        self::assertNull($payload->metadataValue('missing'));
    }
}

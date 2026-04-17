<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\UiComponentResolver;
use PHPUnit\Framework\TestCase;

final class UiComponentResolverRegressionTest extends TestCase
{
    public function testUnknownFieldTypeFallsBackToTextInput(): void
    {
        $resolver = new UiComponentResolver();

        self::assertSame('input:text', $resolver->resolve('UnknownCustomType'));
    }
}

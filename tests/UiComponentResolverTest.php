<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\UiComponentResolver;
use PHPUnit\Framework\TestCase;

final class UiComponentResolverTest extends TestCase
{
    public function testResolverMapsCommonFieldTypes(): void
    {
        $resolver = new UiComponentResolver();

        self::assertSame('input:text', $resolver->resolve('TextType'));
        self::assertSame('input:email', $resolver->resolve('EmailType'));
        self::assertSame('select', $resolver->resolve('SelectType'));
        self::assertSame('collection', $resolver->resolve('CollectionType'));
    }
}

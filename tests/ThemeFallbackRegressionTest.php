<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormThemeKernel;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRendererFactory;
use PHPUnit\Framework\TestCase;

final class ThemeFallbackRegressionTest extends TestCase
{
    public function testNullThemeAliasReturnsDefaultRenderer(): void
    {
        $renderer = (new HtmlRendererFactory(new FormThemeKernel()))->create(null);
        self::assertInstanceOf(HtmlRenderer::class, $renderer);
    }

    public function testMissingThemeAliasReturnsDefaultRenderer(): void
    {
        $renderer = (new HtmlRendererFactory(new FormThemeKernel()))->create('missing-theme');
        self::assertInstanceOf(HtmlRenderer::class, $renderer);
    }
}

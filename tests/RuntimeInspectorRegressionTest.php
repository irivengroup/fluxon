<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Debug\RuntimeInspector;
use Iriven\PhpFormGenerator\Application\FormFactory;
use PHPUnit\Framework\TestCase;

final class RuntimeInspectorRegressionTest extends TestCase
{
    public function testInspectorIsAlwaysStructuredWithoutRuntimeContext(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $data = (new RuntimeInspector())->inspect($form);

        self::assertSame('headless', $data['channel']);
        self::assertArrayHasKey('cache', $data);
        self::assertArrayHasKey('timings', $data);
        self::assertArrayHasKey('metrics', $data);
    }

    public function testInspectorKeepsNullCacheKeyStable(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $data = (new RuntimeInspector())->inspect($form, null, [], false, null);

        self::assertFalse($data['cache']['hit']);
        self::assertNull($data['cache']['key']);
    }
}

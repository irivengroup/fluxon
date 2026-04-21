<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Debug\RuntimeInspector;
use Iriven\PhpFormGenerator\Application\FormFactory;
use PHPUnit\Framework\TestCase;

final class RuntimeInspectorTest extends TestCase
{
    public function testInspectorAlwaysReturnsStructuredPayload(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $data = (new RuntimeInspector())->inspect($form);

        self::assertSame('contact', $data['form']);
        self::assertArrayHasKey('metrics', $data);
        self::assertArrayHasKey('cache', $data);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Ecosystem\FieldPluginRuntimeBridge;
use PHPUnit\Framework\TestCase;

final class FieldPluginRuntimeBridgeTest extends TestCase
{
    public function testEditorPluginIsAppliedAutomaticallyForEditorType(): void
    {
        $bridge = new FieldPluginRuntimeBridge();

        $fields = $bridge->apply([
            ['type' => 'EditorType', 'name' => 'body'],
            ['type' => 'TextType', 'name' => 'title'],
        ]);

        self::assertSame('quill', $fields[0]['component']);
        self::assertArrayNotHasKey('editor', $fields[1]);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Plugins\EditorPlugin;
use PHPUnit\Framework\TestCase;

final class EditorPluginTest extends TestCase
{
    public function testEditorPluginSupportsEditorTypeAndLoadsQuill(): void
    {
        $plugin = new EditorPlugin();
        $field = $plugin->transformField([
            'type' => 'EditorType',
            'name' => 'content',
        ]);

        self::assertTrue($plugin->supportsFieldType('EditorType'));
        self::assertSame('quill', $field['component']);
        self::assertSame('quill', $field['editor']['vendor']);
    }
}

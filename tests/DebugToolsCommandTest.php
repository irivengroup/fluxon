<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Cli\DebugRuntimeCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugSchemaCommand;
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormPluginKernel;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class DebugToolsCommandTest extends TestCase
{
    public function testDebugSchemaReturnsJson(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $output = (new DebugSchemaCommand(new FormSchemaManager(new ArraySchemaExporter()), $form))->run();

        self::assertStringContainsString('{', $output);
    }

    public function testDebugRuntimeReturnsJson(): void
    {
        $output = (new DebugRuntimeCommand(new FormPluginKernel()))->run();

        self::assertStringContainsString('plugins', $output);
        self::assertStringContainsString('extensions', $output);
    }
}

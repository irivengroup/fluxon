<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Cli\CliApplication;
use Iriven\PhpFormGenerator\Application\Cli\MakeFormCommand;
use PHPUnit\Framework\TestCase;

final class CliApplicationTest extends TestCase
{
    public function testApplicationCanRegisterAndRunCommand(): void
    {
        $cli = new CliApplication([new MakeFormCommand()]);

        self::assertContains('make:form', $cli->commands());
        self::assertStringContainsString('DemoForm', $cli->run('make:form', ['DemoForm']));
    }

    public function testUnknownCommandReturnsMessage(): void
    {
        $cli = new CliApplication();

        self::assertSame('Unknown command: missing', $cli->run('missing'));
    }
}

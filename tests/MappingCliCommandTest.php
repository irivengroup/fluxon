<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\Cli\MakeMappingCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugMappingCommand;
use PHPUnit\Framework\TestCase;
final class MappingCliCommandTest extends TestCase
{
    public function testMakeMappingReturnsBlueprint(): void
    {
        self::assertStringContainsString('mapping:', (new MakeMappingCommand())->run());
    }
    public function testDebugMappingReturnsJson(): void
    {
        self::assertNotFalse(json_decode((new DebugMappingCommand())->run(), true));
    }
}

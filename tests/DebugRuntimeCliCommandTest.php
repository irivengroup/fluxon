<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Cli\DebugFormCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugPipelineCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugEventsCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugCacheCommand;
use PHPUnit\Framework\TestCase;

final class DebugRuntimeCliCommandTest extends TestCase
{
    public function testDebugCommandsReturnJson(): void
    {
        self::assertNotFalse(json_decode((new DebugFormCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugPipelineCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugEventsCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugCacheCommand())->run(), true));
    }
}

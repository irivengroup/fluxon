<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Cli\DebugCacheCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugEventsCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugFormCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugPipelineCommand;
use PHPUnit\Framework\TestCase;

final class DebugRuntimeCliRegressionTest extends TestCase
{
    public function testAllRuntimeDebugCommandsAlwaysReturnJson(): void
    {
        self::assertNotFalse(json_decode((new DebugFormCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugPipelineCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugEventsCommand())->run(), true));
        self::assertNotFalse(json_decode((new DebugCacheCommand())->run(), true));
    }
}

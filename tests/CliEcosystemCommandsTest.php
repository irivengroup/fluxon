<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Cli\EcosystemReportCommand;
use Iriven\Fluxon\Application\Cli\PluginListCommand;
use Iriven\Fluxon\Application\Cli\PluginValidateCommand;
use Iriven\Fluxon\Application\Cli\SdkListCommand;
use PHPUnit\Framework\TestCase;

final class CliEcosystemCommandsTest extends TestCase
{
    public function testEcosystemCommandsReturnJson(): void
    {
        self::assertNotFalse(json_decode((new PluginListCommand())->run(), true));
        self::assertNotFalse(json_decode((new PluginValidateCommand())->run(), true));
        self::assertNotFalse(json_decode((new SdkListCommand())->run(), true));
        self::assertNotFalse(json_decode((new EcosystemReportCommand())->run(), true));
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\Cli\DebugHeadlessContractCommand;
use Iriven\PhpFormGenerator\Application\Cli\DebugHeadlessSubmissionCommand;
use PHPUnit\Framework\TestCase;
final class HeadlessCliRegressionTest extends TestCase
{
    public function testDebugHeadlessContractIsAlwaysValidJson(): void
    {
        self::assertNotFalse(json_decode((new DebugHeadlessContractCommand())->run(), true));
    }
    public function testDebugHeadlessSubmissionIsAlwaysValidJson(): void
    {
        self::assertNotFalse(json_decode((new DebugHeadlessSubmissionCommand())->run(), true));
    }
}

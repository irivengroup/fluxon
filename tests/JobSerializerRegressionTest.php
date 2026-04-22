<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Runtime\JobSerializer;
use PHPUnit\Framework\TestCase;

final class JobSerializerRegressionTest extends TestCase
{
    public function testInvalidSerializedPayloadFallsBackToStructuredJob(): void
    {
        $job = (new JobSerializer())->deserialize('{invalid-json');

        self::assertSame('invalid', $job->jobId());
        self::assertSame('unknown', $job->formName());
    }
}

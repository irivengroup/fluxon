<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Runtime\AsyncJobEnvelope;
use PHPUnit\Framework\TestCase;

final class AsyncJobEnvelopeRegressionTest extends TestCase
{
    public function testInvalidEnvelopeIsDetected(): void
    {
        $job = new AsyncJobEnvelope('', '', '', []);

        self::assertFalse($job->isValid());
    }
}

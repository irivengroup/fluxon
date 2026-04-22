<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Runtime\QueueTransport;
use PHPUnit\Framework\TestCase;

final class QueueTransportRegressionTest extends TestCase
{
    public function testUnavailableQueueReturnsStructuredResponse(): void
    {
        $transport = new QueueTransport();
        $transport->setAvailable(false);

        $result = $transport->send(['a' => 1]);

        self::assertSame('unavailable', $result['status']);
        self::assertSame(0, $transport->size());
    }
}

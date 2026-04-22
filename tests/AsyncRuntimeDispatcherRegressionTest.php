<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Tests;

use Iriven\Fluxon\Application\Runtime\AsyncRuntimeDispatcher;
use PHPUnit\Framework\TestCase;

final class AsyncRuntimeDispatcherRegressionTest extends TestCase
{
    public function testDispatcherStillReturnsStructuredQueueResponse(): void
    {
        $dispatcher = new AsyncRuntimeDispatcher();
        $result = $dispatcher->dispatch('submit', 'contact', ['email' => 'john@example.com']);

        self::assertArrayHasKey('transport', $result);
        self::assertArrayHasKey('status', $result);
        self::assertArrayHasKey('queue_size', $result);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Events\FormBuildEvent;
use Iriven\PhpFormGenerator\Application\Events\InMemoryEventDispatcher;
use Iriven\PhpFormGenerator\Application\FormFactory;
use PHPUnit\Framework\TestCase;

final class EventDispatcherRegressionTest extends TestCase
{
    public function testFaultyListenerDoesNotBreakDispatch(): void
    {
        $dispatcher = new InMemoryEventDispatcher();
        $calls = [];

        $dispatcher->addListener(function (): void { throw new \RuntimeException('boom'); });
        $dispatcher->addListener(function () use (&$calls): void { $calls[] = 'after'; });

        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $dispatcher->dispatch(new FormBuildEvent($form));

        self::assertSame(['after'], $calls);
        self::assertSame(2, $dispatcher->listenerCount());
    }

    public function testEmptyDispatcherDoesNothing(): void
    {
        $dispatcher = new InMemoryEventDispatcher();
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $dispatcher->dispatch(new FormBuildEvent($form));

        self::assertSame(0, $dispatcher->listenerCount());
        self::assertTrue(true);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Events\FormBuildEvent;
use Iriven\PhpFormGenerator\Application\Events\InMemoryEventDispatcher;
use Iriven\PhpFormGenerator\Application\FormFactory;
use PHPUnit\Framework\TestCase;

final class EventDispatcherTest extends TestCase
{
    public function testListenersRunInRegistrationOrder(): void
    {
        $dispatcher = new InMemoryEventDispatcher();
        $calls = [];

        $dispatcher->addListener(function () use (&$calls): void { $calls[] = 'a'; });
        $dispatcher->addListener(function () use (&$calls): void { $calls[] = 'b'; });

        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $dispatcher->dispatch(new FormBuildEvent($form));

        self::assertSame(['a', 'b'], $calls);
    }
}

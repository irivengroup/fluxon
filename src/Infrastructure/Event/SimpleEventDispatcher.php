<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Event;

use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Event\FormEvent;

final class SimpleEventDispatcher implements EventDispatcherInterface
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    public function dispatch(string $eventName, FormEvent $event): void
    {
        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            $listener($event);
        }
    }

    public function listen(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName] ??= [];
        $this->listeners[$eventName][] = $listener;
    }
}

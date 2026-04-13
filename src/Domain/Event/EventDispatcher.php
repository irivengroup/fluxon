<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Event;

final class EventDispatcher
{
    /**
     * @var array<string, list<callable>>
     */
    private array $listeners = [];

    public function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName] ??= [];
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(string $eventName, FormEvent $event): FormEvent
    {
        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            $listener($event);
        }

        return $event;
    }
}

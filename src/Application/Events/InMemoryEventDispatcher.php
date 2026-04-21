<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Events;

/** @api */
final class InMemoryEventDispatcher implements EventDispatcherInterface
{
    /** @var array<int, callable> */
    private array $listeners = [];

    public function addListener(callable $listener): void
    {
        $this->listeners[] = $listener;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->listeners as $listener) {
            try {
                $listener($event);
            } catch (\Throwable) {
                continue;
            }
        }
    }
}

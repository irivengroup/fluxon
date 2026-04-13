<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Event\FormEvent;

interface EventDispatcherInterface
{
    public function dispatch(string $eventName, FormEvent $event): void;
    public function listen(string $eventName, callable $listener): void;
}

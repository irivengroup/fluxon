<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Events;

/** @api */
interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}

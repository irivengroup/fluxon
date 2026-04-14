<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface EventSubscriberInterface
{
    /** @return array<string, string> */
    public static function getSubscribedEvents(): array;
}

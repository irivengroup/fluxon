<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Runtime;

use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;

final class HookListenerDefinition
{
    public function __construct(
        private readonly FormHookInterface $hook,
        private readonly int $priority = 0,
    ) {
    }

    public function hook(): FormHookInterface
    {
        return $this->hook;
    }

    public function priority(): int
    {
        return $this->priority;
    }
}

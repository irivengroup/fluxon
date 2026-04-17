<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Runtime;

use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Throwable;

final class PrioritizedHookKernel
{
    /** @var array<string, array<int, HookListenerDefinition>> */
    private array $hooks = [];

    public function __construct(private readonly bool $swallowExceptions = false)
    {
    }

    public function register(FormHookInterface $hook, int $priority = 0): self
    {
        $name = strtolower(trim($hook::getName()));
        $this->hooks[$name] ??= [];
        $this->hooks[$name][] = new HookListenerDefinition($hook, $priority);

        usort(
            $this->hooks[$name],
            static fn (HookListenerDefinition $a, HookListenerDefinition $b): int => $b->priority() <=> $a->priority()
        );

        return $this;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function dispatch(string $name, Form $form, array $context = []): void
    {
        foreach ($this->hooks[strtolower(trim($name))] ?? [] as $definition) {
            try {
                ($definition->hook())($form, $context);
            } catch (Throwable $exception) {
                if (!$this->swallowExceptions) {
                    throw $exception;
                }

                $form->appendError('_form', 'Hook failure: ' . $exception->getMessage());
                $form->setValid(false);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryHookRegistry;
use Throwable;

final class FormHookKernel
{
    private InMemoryHookRegistry $hooks;

    public function __construct(private readonly bool $swallowExceptions = false)
    {
        $this->hooks = new InMemoryHookRegistry();
    }

    public function register(FormHookInterface $hook): self
    {
        $this->hooks->register($hook);

        return $this;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function dispatch(string $name, Form $form, array $context = []): void
    {
        $context = $this->normalizeContext($name, $form, $context);

        foreach ($this->hooks->for($name) as $hook) {
            try {
                $hook($form, $context);
            } catch (Throwable $exception) {
                if (!$this->swallowExceptions) {
                    throw $exception;
                }

                $form->appendError('_form', 'Hook failure: ' . $exception->getMessage());
                $form->setValid(false);
            }
        }
    }

    public function hooks(): InMemoryHookRegistry
    {
        return $this->hooks;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    private function normalizeContext(string $name, Form $form, array $context): array
    {
        return $context + [
            'hook' => $name,
            'form_name' => $form->getName(),
            'submitted' => $form->isSubmitted(),
            'valid' => $form->isCurrentlyValid(),
        ];
    }
}

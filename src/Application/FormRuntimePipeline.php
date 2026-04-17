<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Form\Form;

/**
 * @api
 */
final class FormRuntimePipeline
{
    public function __construct(private readonly ?FormHookKernel $hookKernel = null)
    {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function dispatch(string $stage, Form $form, array $context = []): void
    {
        $this->hookKernel?->dispatch($stage, $form, $context);
    }

    /**
     * @return array<int, string>
     */
    public function stages(): array
    {
        return [
            'before_build',
            'after_build',
            'before_submit',
            'after_submit',
            'before_render',
            'after_render',
            'before_export',
            'after_export',
        ];
    }
}

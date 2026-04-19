<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\FormPluginKernel;

/**
 * @api
 */
final class DebugRuntimeCommand implements CliCommandInterface
{
    public function __construct(private readonly FormPluginKernel $kernel)
    {
    }

    public function name(): string
    {
        return 'debug:runtime';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        return json_encode([
            'plugins' => count($this->kernel->plugins()->all()),
            'field_types' => method_exists($this->kernel->fieldTypes(), 'all') ? $this->kernel->fieldTypes()->all() : [],
            'form_types' => method_exists($this->kernel->formTypes(), 'all') ? $this->kernel->formTypes()->all() : [],
            'extensions' => count($this->kernel->extensions()->all()),
        ], JSON_PRETTY_PRINT) ?: '{}';
    }
}

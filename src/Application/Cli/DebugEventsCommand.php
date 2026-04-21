<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Events\FormEvents;

/** @api */
final class DebugEventsCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'debug:events';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        return json_encode([
            FormEvents::PRE_BUILD,
            FormEvents::POST_BUILD,
            FormEvents::PRE_RENDER,
            FormEvents::POST_RENDER,
            FormEvents::PRE_SUBMIT,
            FormEvents::POST_SUBMIT,
        ], JSON_PRETTY_PRINT) ?: '[]';
    }
}

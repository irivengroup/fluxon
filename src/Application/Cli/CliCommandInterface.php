<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

/**
 * @api
 */
interface CliCommandInterface
{
    public function name(): string;

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string;
}

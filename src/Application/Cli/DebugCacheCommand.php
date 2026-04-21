<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Dx\SchemaCacheKeyGenerator;

/** @api */
final class DebugCacheCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'debug:cache';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $key = (new SchemaCacheKeyGenerator())->generate('contact', ['theme' => 'default']);

        return json_encode(['key' => $key], JSON_PRETTY_PRINT) ?: '{}';
    }
}

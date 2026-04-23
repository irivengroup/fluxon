<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Cli;

use Iriven\Fluxon\Application\Sdk\SdkCatalog;

final class SdkListCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'sdk:list';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        return json_encode((new SdkCatalog())->all(), JSON_PRETTY_PRINT) ?: '[]';
    }
}

<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Sdk;

final class SdkCatalog
{
    /**
     * @return array<int, string>
     */
    public function all(): array
    {
        return [
            'php',
            'javascript-manifest',
            'frontend-component-registry',
        ];
    }
}

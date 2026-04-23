<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Ecosystem;

final class CompatibilityMatrix
{
    /**
     * @return array<string, mixed>
     */
    public function report(string $schemaVersion = '7.0', string $runtimeVersion = '7.0'): array
    {
        return [
            'schema_version' => $schemaVersion,
            'runtime_version' => $runtimeVersion,
            'compatible' => true,
        ];
    }
}

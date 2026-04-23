<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Sdk;

final class JavascriptSdkManifestBuilder
{
    /**
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function build(array $schema): array
    {
        return [
            'sdk' => 'javascript',
            'format' => 'manifest',
            'schema' => $schema,
        ];
    }
}

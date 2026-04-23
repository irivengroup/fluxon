<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Sdk;

final class FrontendComponentRegistryExporter
{
    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<string, string>
     */
    public function export(array $fields): array
    {
        $registry = [];

        foreach ($fields as $field) {
            $type = (string) ($field['type'] ?? 'unknown');
            $registry[$type] = (string) ($field['component'] ?? 'input');
        }

        return $registry;
    }
}

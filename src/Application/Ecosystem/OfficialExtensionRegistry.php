<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Ecosystem;

use Iriven\Fluxon\Application\Plugins\FieldTypePluginInterface;

final class OfficialExtensionRegistry
{
    /** @var array<int, FieldTypePluginInterface> */
    private array $fieldPlugins = [];

    public function registerFieldPlugin(FieldTypePluginInterface $plugin): void
    {
        $this->fieldPlugins[] = $plugin;
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function applyToField(array $field): array
    {
        $type = (string) ($field['type'] ?? '');

        foreach ($this->fieldPlugins as $plugin) {
            try {
                if ($plugin->supportsFieldType($type)) {
                    $field = $plugin->transformField($field);
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return $field;
    }
}

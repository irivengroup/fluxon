<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Ecosystem;

use Iriven\Fluxon\Application\Plugins\EditorPlugin;

final class FieldPluginRuntimeBridge
{
    private OfficialExtensionRegistry $registry;

    public function __construct(?OfficialExtensionRegistry $registry = null)
    {
        $this->registry = $registry ?? new OfficialExtensionRegistry();
        $this->registry->registerFieldPlugin(new EditorPlugin());
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<int, array<string, mixed>>
     */
    public function apply(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[] = $this->registry->applyToField($field);
        }

        return $result;
    }
}

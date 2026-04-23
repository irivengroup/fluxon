<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Plugins;

interface FieldTypePluginInterface extends OfficialPluginInterface
{
    public function supportsFieldType(string $fieldType): bool;

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function transformField(array $field): array;
}

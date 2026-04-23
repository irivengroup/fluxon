<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Plugins;

final class EditorPlugin implements FieldTypePluginInterface
{
    public function name(): string
    {
        return 'editor';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function register(PluginContext $context): void
    {
        $context->log('editor plugin registered');
    }

    public function supportsFieldType(string $fieldType): bool
    {
        return $fieldType === 'EditorType';
    }

    /**
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function transformField(array $field): array
    {
        $field['component'] = 'quill';
        $field['editor'] = [
            'vendor' => 'quill',
            'mode' => 'rich-text',
        ];

        return $field;
    }
}

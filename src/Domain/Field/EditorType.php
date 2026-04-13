<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class EditorType extends TextareaType
{
    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $class = trim((string) ($options['attr']['class'] ?? ''));
        $options['attr']['class'] = trim($class . ' editor');
        return $options;
    }
}

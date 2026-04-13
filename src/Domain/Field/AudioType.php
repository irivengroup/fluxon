<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class AudioType extends FileType
{
    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['accept'] ??= 'audio/*';
        return $options;
    }
}

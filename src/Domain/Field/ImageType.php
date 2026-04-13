<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class ImageType extends FileType
{
    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['accept'] ??= 'image/*';
        return $options;
    }
}

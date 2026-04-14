<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class ImageType extends FileType
{
    /** @return list<string> */
    public static function allowedMimeTypes(): array
    {
        return [
            'image/avif',
            'image/bmp',
            'image/gif',
            'image/heic',
            'image/heif',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/svg+xml',
            'image/tiff',
            'image/webp',
            'image/x-icon',
        ];
    }

    public static function acceptAttribute(): ?string
    {
        return 'image/*';
    }
}

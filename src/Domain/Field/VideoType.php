<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class VideoType extends FileType
{
    /** @return array<int, string> */
    public static function allowedMimeTypes(): array
    {
        return [
            'video/3gpp',
            'video/3gpp2',
            'video/avi',
            'video/mp2t',
            'video/mp4',
            'video/mpeg',
            'video/ogg',
            'video/quicktime',
            'video/webm',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/x-matroska',
        ];
    }

    public static function acceptAttribute(): ?string
    {
        return 'video/*';
    }
}

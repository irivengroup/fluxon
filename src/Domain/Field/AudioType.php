<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class AudioType extends FileType
{
    /** @return list<string> */
    public static function allowedMimeTypes(): array
    {
        return [
            'audio/aac',
            'audio/flac',
            'audio/m4a',
            'audio/midi',
            'audio/mp3',
            'audio/mp4',
            'audio/mpeg',
            'audio/ogg',
            'audio/opus',
            'audio/wav',
            'audio/webm',
            'audio/x-aac',
            'audio/x-flac',
            'audio/x-m4a',
            'audio/x-wav',
            'audio/x-ms-wma',
        ];
    }

    public static function acceptAttribute(): ?string
    {
        return 'audio/*';
    }
}

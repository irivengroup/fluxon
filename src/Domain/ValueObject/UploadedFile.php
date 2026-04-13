<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\ValueObject;

final class UploadedFile
{
    public function __construct(
        public readonly string $clientName,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $tmpPath,
        public readonly int $error = 0
    ) {
    }
}

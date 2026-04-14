<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class MaxFileSize implements ConstraintInterface
{
    public function __construct(
        private readonly int $maxBytes,
        private readonly string $message = 'The uploaded file is too large.',
    ) {
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        if (!is_array($value)) {
            return [];
        }

        $size = $value['size'] ?? null;
        if (!is_int($size)) {
            return [];
        }

        return $size <= $this->maxBytes ? [] : [$this->message];
    }
}

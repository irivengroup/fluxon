<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class MimeType implements ConstraintInterface
{
    /** @param list<string> $allowed */
    public function __construct(
        private readonly array $allowed,
        private readonly string $message = 'The uploaded file has an invalid MIME type.',
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if (!is_array($value)) {
            return [];
        }

        $mimeType = $value['mimeType'] ?? $value['type'] ?? null;
        if ($mimeType === null) {
            return [];
        }

        return in_array((string) $mimeType, $this->allowed, true) ? [] : [$this->message];
    }
}

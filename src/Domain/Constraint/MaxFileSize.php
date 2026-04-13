<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class MaxFileSize implements ConstraintInterface
{
    public function __construct(private readonly int $maxBytes, private readonly string $message = 'File is too large.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || !is_array($value) || !isset($value['size'])) {
            return [];
        }

        return (int) $value['size'] <= $this->maxBytes ? [] : [$this->message];
    }
}

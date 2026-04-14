<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Min implements ConstraintInterface
{
    public function __construct(
        private readonly int|float $min,
        private readonly string $message = 'This value is too small.',
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return is_numeric($value) && (float) $value >= (float) $this->min ? [] : [$this->message];
    }
}

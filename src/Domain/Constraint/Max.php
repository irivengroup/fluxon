<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Max implements ConstraintInterface
{
    public function __construct(
        private readonly int|float $max,
        private readonly string $message = 'This value is too large.',
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return is_numeric($value) && (float) $value <= (float) $this->max ? [] : [$this->message];
    }
}

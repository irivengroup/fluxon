<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Range implements ConstraintInterface
{
    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max,
        private readonly string $message = 'This value is out of range.',
    ) {
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (!is_numeric($value)) {
            return [$this->message];
        }

        $numeric = (float) $value;

        return $numeric >= (float) $this->min && $numeric <= (float) $this->max ? [] : [$this->message];
    }
}

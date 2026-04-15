<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Countable;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Count implements ConstraintInterface
{
    public function __construct(
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly string $message = 'This collection has an invalid number of items.',
    ) {
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        $count = $this->countValue($value);

        if ($count === null) {
            return [$this->message];
        }

        if ($this->violatesMin($count) || $this->violatesMax($count)) {
            return [$this->message];
        }

        return [];
    }

    private function countValue(mixed $value): ?int
    {
        if ($value === null) {
            return 0;
        }

        if (is_array($value) || $value instanceof Countable) {
            return count($value);
        }

        return null;
    }

    private function violatesMin(int $count): bool
    {
        return $this->min !== null && $count < $this->min;
    }

    private function violatesMax(int $count): bool
    {
        return $this->max !== null && $count > $this->max;
    }
}

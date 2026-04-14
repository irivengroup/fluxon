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
        if ($value === null) {
            $count = 0;
        } elseif (is_array($value) || $value instanceof Countable) {
            $count = count($value);
        } else {
            return [$this->message];
        }

        if ($this->min !== null && $count < $this->min) {
            return [$this->message];
        }

        if ($this->max !== null && $count > $this->max) {
            return [$this->message];
        }

        return [];
    }
}

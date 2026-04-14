<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Length implements ConstraintInterface
{
    public function __construct(
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly string $message = 'This value has an invalid length.',
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null) {
            return [];
        }

        $length = mb_strlen((string) $value);

        if ($this->min !== null && $length < $this->min) {
            return [$this->message];
        }

        if ($this->max !== null && $length > $this->max) {
            return [$this->message];
        }

        return [];
    }
}

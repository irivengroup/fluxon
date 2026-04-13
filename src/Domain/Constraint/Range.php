<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Range implements ConstraintInterface
{
    public function __construct(
        private readonly float|int $min,
        private readonly float|int $max,
        private readonly string $message = 'Value is out of range.'
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $numeric = (float) $value;
        return $numeric >= $this->min && $numeric <= $this->max ? [] : [$this->message];
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Min implements ConstraintInterface
{
    public function __construct(private readonly float|int $min, private readonly string $message = 'Value is too small.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return (float) $value >= $this->min ? [] : [$this->message];
    }
}

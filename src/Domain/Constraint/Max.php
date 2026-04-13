<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Max implements ConstraintInterface
{
    public function __construct(private readonly float|int $max, private readonly string $message = 'Value is too large.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return (float) $value <= $this->max ? [] : [$this->message];
    }
}

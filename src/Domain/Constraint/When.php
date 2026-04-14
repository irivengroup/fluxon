<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Closure;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class When implements ConstraintInterface
{
    /** @var Closure(mixed, array<string, mixed>): bool */
    private Closure $condition;

    /**
     * @param array<int, ConstraintInterface> $constraints
     * @param callable(mixed, array<string, mixed>): bool $condition
     */
    public function __construct(
        callable $condition,
        private readonly array $constraints,
    ) {
        $this->condition = $condition(...);
    }

    /**
     * @param array<string, mixed> $context
     * @return array<int, string>
     */
    public function validate(mixed $value, array $context = []): array
    {
        if (!(($this->condition)($value, $context))) {
            return [];
        }

        $errors = [];
        foreach ($this->constraints as $constraint) {
            foreach ($constraint->validate($value, $context) as $error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }
}

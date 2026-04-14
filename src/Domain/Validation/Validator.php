<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Validation;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Validator
{
    /**
     * @param list<ConstraintInterface> $constraints
     * @param array<string, mixed> $context
     * @return list<string>
     */
    public function validate(mixed $value, array $constraints, array $context = []): array
    {
        $errors = [];
        foreach ($constraints as $constraint) {
            foreach ($constraint->validate($value, $context) as $error) {
                $errors[] = $error;
            }
        }
        return $errors;
    }
}

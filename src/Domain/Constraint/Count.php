<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Count implements ConstraintInterface
{
    public function __construct(
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly string $message = 'Invalid item count.'
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if (!is_array($value)) {
            return [];
        }

        $count = count($value);
        $errors = [];
        if ($this->min !== null && $count < $this->min) {
            $errors[] = 'Minimum item count is ' . $this->min . '.';
        }
        if ($this->max !== null && $count > $this->max) {
            $errors[] = 'Maximum item count is ' . $this->max . '.';
        }

        return $errors;
    }
}

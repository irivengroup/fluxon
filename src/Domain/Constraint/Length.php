<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Length implements ConstraintInterface
{
    public function __construct(
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly string $message = 'Invalid length.'
    ) {
    }

    public function validate(mixed $value, array $context = []): array
    {
        $length = mb_strlen((string) ($value ?? ''));
        $errors = [];
        if ($this->min !== null && $length < $this->min) {
            $errors[] = 'Minimum length is ' . $this->min . '.';
        }
        if ($this->max !== null && $length > $this->max) {
            $errors[] = 'Maximum length is ' . $this->max . '.';
        }
        return $errors;
    }
}

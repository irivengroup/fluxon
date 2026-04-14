<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Regex implements ConstraintInterface
{
    public function __construct(
        private readonly string $pattern,
        private readonly string $message = 'This value has an invalid format.',
    ) {
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return preg_match($this->pattern, (string) $value) === 1 ? [] : [$this->message];
    }
}

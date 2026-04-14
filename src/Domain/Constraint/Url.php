<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Url implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'This value is not a valid URL.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return filter_var((string) $value, FILTER_VALIDATE_URL) !== false ? [] : [$this->message];
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Required implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'This value is required.')
    {
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '' || $value === [] || $value === false) {
            return [$this->message];
        }

        return [];
    }
}

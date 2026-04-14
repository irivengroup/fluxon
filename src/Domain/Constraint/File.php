<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class File implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'A file upload is required.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '' || (is_array($value) && $value === [])) {
            return [$this->message];
        }

        return [];
    }
}

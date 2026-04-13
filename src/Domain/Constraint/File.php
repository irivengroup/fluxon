<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class File implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'Invalid uploaded file.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        return is_array($value) && isset($value['name']) ? [] : [$this->message];
    }
}

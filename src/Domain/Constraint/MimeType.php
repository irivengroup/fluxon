<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class MimeType implements ConstraintInterface
{
    /** @param list<string> $allowed */
    public function __construct(private readonly array $allowed, private readonly string $message = 'Invalid file type.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || !is_array($value) || !isset($value['type'])) {
            return [];
        }

        return in_array((string) $value['type'], $this->allowed, true) ? [] : [$this->message];
    }
}

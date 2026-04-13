<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Choice implements ConstraintInterface
{
    /** @param list<string|int> $choices */
    public function __construct(private readonly array $choices, private readonly string $message = 'Invalid choice.')
    {
    }

    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (!in_array($item, $this->choices, true)) {
                    return [$this->message];
                }
            }
            return [];
        }

        return in_array($value, $this->choices, true) ? [] : [$this->message];
    }
}

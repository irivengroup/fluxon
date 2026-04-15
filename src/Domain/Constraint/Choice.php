<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Choice implements ConstraintInterface
{
    use TranslatableConstraintMessageTrait;

    /** @param array<int, string|int> $choices */
    public function __construct(
        private readonly array $choices,
        private readonly string $message = 'This value is not a valid choice.',
    ) {
    }

    /**
     * @param array<string, mixed> $context
     * @return array<int, string>
     */
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return $this->validateArrayChoice($value, $context);
        }

        return $this->isAllowedChoice($value)
            ? []
            : [$this->messageFromContext($context, 'choice.invalid', $this->message)];
    }

    /**
     * @param array<int|string, mixed> $value
     * @param array<string, mixed> $context
     * @return array<int, string>
     */
    private function validateArrayChoice(array $value, array $context): array
    {
        foreach ($value as $item) {
            if (!$this->isAllowedChoice($item)) {
                return [$this->messageFromContext($context, 'choice.invalid', $this->message)];
            }
        }

        return [];
    }

    private function isAllowedChoice(mixed $value): bool
    {
        return in_array($value, $this->choices, true);
    }
}

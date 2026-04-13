<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Choice implements ConstraintInterface
{
    public function __construct(private readonly array $choices, private readonly bool $multiple = false, private readonly string $message = 'Invalid choice.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        $allowed = array_map(static fn($v) => (string) $v, $this->choices);
        if ($this->multiple) {
            if (!is_array($value)) { return [new ValidationError($this->message, $context['field'] ?? null)]; }
            foreach ($value as $entry) {
                if (!in_array((string) $entry, $allowed, true)) {
                    return [new ValidationError($this->message, $context['field'] ?? null)];
                }
            }
            return [];
        }
        return in_array((string) $value, $allowed, true) ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

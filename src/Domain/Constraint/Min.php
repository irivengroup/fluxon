<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Min implements ConstraintInterface
{
    public function __construct(private readonly int|float $min, private readonly string $message = 'Value is too low.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        return (float) $value >= $this->min ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

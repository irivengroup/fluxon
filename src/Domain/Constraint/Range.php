<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Range implements ConstraintInterface
{
    public function __construct(private readonly int|float $min, private readonly int|float $max, private readonly string $message = 'Value is out of range.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        $n = (float) $value;
        return $n >= $this->min && $n <= $this->max ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

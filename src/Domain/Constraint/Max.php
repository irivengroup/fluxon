<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Max implements ConstraintInterface
{
    public function __construct(private readonly int|float $max, private readonly string $message = 'Value is too high.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        return (float) $value <= $this->max ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

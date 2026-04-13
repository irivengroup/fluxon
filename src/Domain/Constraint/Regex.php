<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Regex implements ConstraintInterface
{
    public function __construct(private readonly string $pattern, private readonly string $message = 'Invalid format.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        return preg_match($this->pattern, (string) $value) === 1 ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

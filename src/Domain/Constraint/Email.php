<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Email implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'Invalid email address.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        return filter_var((string) $value, FILTER_VALIDATE_EMAIL) ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

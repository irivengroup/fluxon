<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Required implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'This value is required.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '' || $value === [] || $value === false) {
            return [new ValidationError($this->message, $context['field'] ?? null)];
        }
        return [];
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Count implements ConstraintInterface
{
    public function __construct(private readonly ?int $min = null, private readonly ?int $max = null, private readonly string $message = 'Invalid item count.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if (!is_array($value)) { return []; }
        $count = count($value);
        if (($this->min !== null and $count < $this->min) || ($this->max !== null and $count > $this->max)) {
            return [new ValidationError($this->message, $context['field'] ?? null)];
        }
        return [];
    }
}

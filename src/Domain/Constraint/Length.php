<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
final class Length implements ConstraintInterface
{
    public function __construct(private readonly ?int $min = null, private readonly ?int $max = null, private readonly string $minMessage = 'Too short.', private readonly string $maxMessage = 'Too long.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null) { return []; }
        $length = mb_strlen((string) $value);
        $errors = [];
        if ($this->min !== null && $length < $this->min) { $errors[] = new ValidationError($this->minMessage, $context['field'] ?? null); }
        if ($this->max !== null && $length > $this->max) { $errors[] = new ValidationError($this->maxMessage, $context['field'] ?? null); }
        return $errors;
    }
}

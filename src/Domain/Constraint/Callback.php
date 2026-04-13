<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
final class Callback implements ConstraintInterface
{
    public function __construct(private readonly \Closure $callback) {}
    public function validate(mixed $value, array $context = []): array
    {
        $result = ($this->callback)($value, $context);
        return is_array($result) ? $result : [];
    }
}

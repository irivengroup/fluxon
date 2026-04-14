<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Closure;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Callback implements ConstraintInterface
{
    /** @var Closure(mixed, array<string, mixed>): array<int, string> */
    private Closure $callback;

    /** @param callable(mixed, array<string, mixed>): array<int, string> $callback */
    public function __construct(callable $callback)
    {
        $this->callback = $callback(...);
    }

    /** @param array<string, mixed> $context @return array<int, string> */
    public function validate(mixed $value, array $context = []): array
    {
        return ($this->callback)($value, $context);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;

final class Callback implements ConstraintInterface
{
    /** @var callable(mixed,array): list<string> */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function validate(mixed $value, array $context = []): array
    {
        return ($this->callback)($value, $context);
    }
}

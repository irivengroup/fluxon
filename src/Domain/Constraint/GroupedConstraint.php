<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Constraint;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\ValidationGroupAwareInterface;

final class GroupedConstraint implements ConstraintInterface, ValidationGroupAwareInterface
{
    /**
     * @param array<int, string> $groups
     */
    public function __construct(
        private readonly ConstraintInterface $inner,
        private readonly array $groups = ['Default'],
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function validate(mixed $value, array $context = []): array
    {
        return $this->inner->validate($value, $context);
    }

    /**
     * @return array<int, string>
     */
    public function groups(): array
    {
        return $this->groups;
    }
}

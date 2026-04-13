<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;

interface ConstraintInterface
{
    /**
     * @return list<ValidationError>
     */
    public function validate(mixed $value, array $context = []): array;
}

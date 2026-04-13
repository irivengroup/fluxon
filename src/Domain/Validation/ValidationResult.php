<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Validation;

final class ValidationResult
{
    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(
        public readonly array $errors = []
    ) {
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }
}

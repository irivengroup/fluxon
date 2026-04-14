<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Validation;

final class ValidationResult
{
    /** @param list<string> $errors */
    public function __construct(
        public readonly bool $valid,
        public readonly array $errors = [],
    ) {
    }
}

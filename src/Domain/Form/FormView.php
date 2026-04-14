<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

final class FormView
{
    /**
     * @param array<string, mixed> $vars
     * @param list<FormView> $children
     * @param list<string> $errors
     * @param list<Fieldset> $fieldsets
     */
    public function __construct(
        public readonly string $name,
        public readonly string $fullName,
        public readonly string $id,
        public readonly string $type,
        public readonly mixed $value,
        public readonly array $vars = [],
        public readonly array $children = [],
        public readonly array $errors = [],
        public readonly array $fieldsets = [],
        public readonly bool $submitted = false,
        public readonly bool $valid = true,
    ) {
    }
}

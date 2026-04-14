<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;

final class FieldConfig
{
    /**
     * @param array<string, mixed> $options
     * @param array<int, ConstraintInterface> $constraints
     * @param array<int, DataTransformerInterface> $transformers
     * @param array<string, FieldConfig> $children
     * @param array<string, mixed> $entryOptions
     * @param array<int, Fieldset> $fieldsets
     */
    public function __construct(
        public string $name,
        public string $typeClass,
        public array $options = [],
        public array $constraints = [],
        public array $transformers = [],
        public array $children = [],
        public bool $compound = false,
        public bool $collection = false,
        public ?string $entryType = null,
        public array $entryOptions = [],
        public array $fieldsets = [],
    ) {
    }
}

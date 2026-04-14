<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;

final class FieldConfig
{
    /**
     * @param list<ConstraintInterface> $constraints
     * @param list<DataTransformerInterface> $transformers
     * @param array<string, FieldConfig> $children
     * @param list<Fieldset> $fieldsets
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

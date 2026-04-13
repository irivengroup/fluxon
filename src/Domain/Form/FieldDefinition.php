<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;

final class FieldDefinition
{
    /** @param list<ConstraintInterface> $constraints */
    public function __construct(
        public readonly string $name,
        public readonly FieldTypeInterface $type,
        public readonly array $options = [],
        public readonly array $constraints = [],
        public mixed $value = null,
        public array $errors = [],
    ) {
    }

    public function label(): string
    {
        return (string) ($this->options['label'] ?? ucfirst(str_replace('_', ' ', $this->name)));
    }

    public function id(): string
    {
        return (string) ($this->options['id'] ?? $this->name);
    }

    public function mapped(): bool
    {
        return (bool) ($this->options['mapped'] ?? true);
    }
}

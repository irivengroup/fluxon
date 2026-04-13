<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;

final class Field
{
    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(
        private readonly string $name,
        private readonly FieldTypeInterface $type,
        private array $options = [],
        private mixed $data = null,
        private array $errors = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FieldTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOption(string $name, mixed $value): void
    {
        $this->options[$name] = $value;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * @return list<ValidationError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param list<ValidationError> $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }
}

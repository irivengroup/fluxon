<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain;

use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

final class Label
{
    private Attributes $attributes;

    public function __construct(private readonly string $text, array $attributes = [])
    {
        $this->attributes = new Attributes($attributes);
    }

    public function text(): string
    {
        return $this->text;
    }

    public function attributes(): Attributes
    {
        return $this->attributes;
    }

    public function render(string $fieldType, string $for): string
    {
        if (in_array($fieldType, ['hidden', 'submit', 'reset', 'button'], true)) {
            return '';
        }

        $this->attributes->set('for', $for)->ignore(['id', 'type']);

        return '<label' . $this->attributes->render() . '>'
            . htmlspecialchars(rtrim($this->text, ': '), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . ': </label>';
    }
}

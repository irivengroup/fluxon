<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class LabelView
{
    public function __construct(
        private readonly string $text,
        private readonly HtmlAttributes $attributes,
    ) {
    }

    public function renderFor(string $fieldType, string $id): string
    {
        if (in_array($fieldType, ['hidden', 'submit', 'reset', 'button'], true)) {
            return '';
        }

        $attributes = clone $this->attributes;
        $attributes->set('for', $id)->set('fieldtype', $fieldType)->ignore(['id', 'label', 'type', 'fieldtype']);

        return '<label' . $attributes->render('label') . '>' . Escaper::text(rtrim($this->text, ': ')) . ': </label>';
    }
}

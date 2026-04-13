<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Presentation\Html\Escaper;

class ChoiceElement extends AbstractElement
{
    public function __construct(
        string $label,
        protected readonly array $options,
        string $type,
        array $attributes = [],
    ) {
        parent::__construct($label, $attributes);
        $this->attributes->set('type', $type);
    }

    public function render(): string
    {
        $type = (string) $this->attributes->get('type');
        $value = $this->attributes->get('value', null);

        if (count($this->options) < 2 && $type === 'checkbox') {
            $checked = (bool) $value;
            $visible = clone $this->attributes;
            if ($checked) {
                $visible->set('checked', 'checked');
            }
            $visible->set('value', '1');
            $hidden = new InputElement($this->name(), 'hidden', [
                'name' => $this->name(),
                'value' => '0',
            ]);

            return $this->renderLabel()
                . '<input' . $visible->render('checkbox') . '>'
                . $hidden->render();
        }

        $selectedValues = is_array($value) ? $value : ($value === null || $value === '' ? [] : [$value]);
        $name = $this->name();
        if ($type === 'checkbox' && !str_ends_with($name, '[]')) {
            $name .= '[]';
        }

        $base = clone $this->attributes;
        $base->ignore(['id', 'value', 'checked', 'required']);
        $base->set('name', $name);

        $html = $this->renderLabel();
        $i = 0;
        foreach ($this->options as $optionValue => $optionLabel) {
            $id = (string) $this->attributes->get('id') . '-' . $i++;
            $input = clone $base;
            $input->set('id', $id)->set('value', (string) $optionValue);
            if (in_array((string) $optionValue, array_map('strval', $selectedValues), true)) {
                $input->set('checked', 'checked');
            }

            $html .= '<label for="' . Escaper::attr($id) . '">';
            $html .= '<input' . $input->render($type) . '>';
            $html .= Escaper::text((string) $optionLabel) . '</label>';
        }

        return $html;
    }
}

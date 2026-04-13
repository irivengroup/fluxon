<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

final class ChoiceElement extends AbstractElement
{
    public function __construct(string $label, private readonly array $options = [], array $attributes = [], string $type = 'checkbox')
    {
        parent::__construct($label, $attributes);
        $this->attributes->set('type', $type);
    }

    public function render(): string
    {
        $type = (string) $this->attributes->get('type');

        if (count($this->options) < 2 && $type === 'checkbox') {
            $inputAttributes = $this->attributes->all();
            $checked = (bool) ($inputAttributes['value'] ?? false);
            $inputAttributes['value'] = '1';
            $base = new InputElement($this->label->text(), $inputAttributes, 'checkbox');
            if ($checked) {
                $base->attributes()->set('checked', 'checked');
            }
            $hidden = new InputElement($this->label->text(), ['name' => (string) $this->attributes->get('name'), 'value' => '0'], 'hidden');
            return $base->render() . $hidden->render();
        }

        $values = $this->attributes->get('value', []);
        if (!is_array($values)) {
            $values = [$values];
        }
        $values = array_map('strval', $values);

        $name = (string) $this->attributes->get('name');
        if ($type === 'checkbox' && !str_ends_with($name, '[]')) {
            $name .= '[]';
        }

        $common = new Attributes($this->attributes->all());
        $common->set('name', $name)->ignore(['id', 'value', 'checked', 'required', 'type']);

        $baseId = (string) $this->attributes->get('id');
        $html = $this->renderLabel();
        $i = 0;
        foreach ($this->options as $value => $caption) {
            $optionId = $baseId . '-' . $i;
            $html .= '<label for="' . $this->escape($optionId) . '">';
            $html .= '<input id="' . $this->escape($optionId) . '" type="' . $this->escape($type) . '" value="' . $this->escape((string) $value) . '"' . $common->render();
            if (in_array((string) $value, $values, true)) {
                $html .= ' checked="checked"';
            }
            $html .= '>' . $this->escape((string) $caption) . '</label>';
            $i++;
        }
        return $html;
    }
}

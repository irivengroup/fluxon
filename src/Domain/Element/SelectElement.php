<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

final class SelectElement extends AbstractElement
{
    private bool $selected = false;

    public function __construct(string $label, private readonly array $options = [], array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes
            ->set('type', 'select')
            ->set('placeholder', (string) $this->attributes->get('placeholder', 'Make a choice ...'));
    }

    public function render(): string
    {
        $values = $this->attributes->get('value', []);
        if (!is_array($values)) {
            $values = [$values];
        }
        $values = array_map('strval', $values);
        $multiple = (string) $this->attributes->get('multiple', '') === 'multiple';
        if ($multiple) {
            $this->attributes->set('size', $this->attributes->get('size', 3));
            $name = (string) $this->attributes->get('name');
            if (!str_ends_with($name, '[]')) {
                $this->attributes->set('name', $name . '[]');
            }
        }

        $placeholder = (string) $this->attributes->get('placeholder', '');
        $this->attributes->ignore(['value', 'selected', 'optgroup-attributes', 'option-attributes', 'placeholder']);

        $html = $this->renderLabel() . '<select' . $this->attributes->render() . '>';
        if (($values[0] ?? '') === '' && $placeholder !== '') {
            $html .= '<option value="" disabled selected>' . $this->escape($placeholder) . '</option>';
        }
        foreach ($this->options as $index => $data) {
            $html .= is_array($data)
                ? $this->renderOptgroup((string) $index, $data, $values)
                : $this->renderOption((string) $index, (string) $data, $values);
        }
        return $html . '</select>';
    }

    private function renderOptgroup(string $label, array $options, array $values): string
    {
        $groupAttributes = new Attributes((array) $this->attributes->get('optgroup-attributes', []));
        $groupAttributes->ignore('label');
        $html = '<optgroup label="' . $this->escape($label) . '"' . $groupAttributes->render() . '>';
        foreach ($options as $value => $text) {
            if (is_array($text)) {
                $html .= $this->renderOptgroup((string) $value, $text, $values);
            } else {
                $html .= $this->renderOption((string) $value, (string) $text, $values);
            }
        }
        return $html . '</optgroup>';
    }

    private function renderOption(string $value, string $text, array $values): string
    {
        $optionAttributes = new Attributes((array) $this->attributes->get('option-attributes', []));
        $optionAttributes->ignore(['value', 'selected', 'placeholder']);
        $html = '<option value="' . $this->escape($value) . '"';
        if (!$this->selected && in_array($value, $values, true)) {
            $html .= ' selected="selected"';
            $this->selected = true;
        }
        return $html . $optionAttributes->render() . '>' . $this->escape($text) . '</option>';
    }
}

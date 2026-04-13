<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Presentation\Html\Escaper;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlAttributes;

class SelectElement extends AbstractElement
{
    private bool $selectedOnce = false;

    public function __construct(string $label, protected readonly array $options, array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes
            ->set('type', 'select')
            ->set('placeholder', $this->attributes->get('placeholder', 'Make a choice ...'));
    }

    public function render(): string
    {
        $values = $this->attributes->get('value', []);
        if (!is_array($values)) {
            $values = [$values];
        }
        $values = array_map('strval', $values);

        $multiple = $this->attributes->get('multiple') === 'multiple' || $this->attributes->get('multiple') === true;
        $name = $this->name();

        $attrs = clone $this->attributes;
        if ($multiple) {
            $attrs->set('size', $attrs->get('size', 3));
            if (!str_ends_with($name, '[]')) {
                $name .= '[]';
            }
        }
        $attrs->set('name', $name)->ignore(['value', 'selected', 'optgroup-attributes', 'option-attributes', 'placeholder']);

        $html = $this->renderLabel();
        $html .= '<select' . $attrs->render('select') . '>';

        if (($values[0] ?? '') === '' && $this->attributes->has('placeholder')) {
            $html .= '<option value="" disabled selected>' . Escaper::text((string) $this->attributes->get('placeholder')) . '</option>';
        }

        foreach ($this->options as $value => $label) {
            $html .= is_array($label)
                ? $this->renderOptgroup((string) $value, $label, $values)
                : $this->renderOption((string) $value, (string) $label, $values);
        }

        return $html . '</select>';
    }

    private function renderOptgroup(string $label, array $options, array $selectedValues): string
    {
        $groupAttributes = '';
        $optionAttributes = '';

        if (is_array($this->attributes->get('optgroup-attributes'))) {
            $group = new HtmlAttributes($this->attributes->get('optgroup-attributes'));
            $groupAttributes = $group->render('optgroup');
        }

        if (is_array($this->attributes->get('option-attributes'))) {
            $option = new HtmlAttributes($this->attributes->get('option-attributes'));
            $optionAttributes = $option->render('option');
        }

        $html = '<optgroup label="' . Escaper::attr($label) . '"' . $groupAttributes . '>';
        foreach ($options as $value => $text) {
            if (is_array($text)) {
                $html .= $this->renderOptgroup((string) $value, $text, $selectedValues);
                continue;
            }
            $html .= $this->renderOption((string) $value, (string) $text, $selectedValues, $optionAttributes);
        }
        return $html . '</optgroup>';
    }

    private function renderOption(string $value, string $text, array $selectedValues, string $extraAttributes = ''): string
    {
        $selected = !$this->selectedOnce && in_array($value, $selectedValues, true);
        if ($selected) {
            $this->selectedOnce = true;
        }

        return '<option value="' . Escaper::attr($value) . '"'
            . ($selected ? ' selected="selected"' : '')
            . $extraAttributes
            . '>'
            . Escaper::text($text)
            . '</option>';
    }
}

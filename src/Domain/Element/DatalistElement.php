<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

final class DatalistElement extends InputElement
{
    public function __construct(string $label, private readonly array $options = [], array $attributes = [])
    {
        parent::__construct($label, $attributes, 'text');
        $this->attributes->set('list', 'datalist-' . $this->attributes->get('id'));
    }

    public function render(): string
    {
        $html = parent::render();
        $html .= '<datalist id="' . $this->escape((string) $this->attributes->get('list')) . '">';
        foreach ($this->options as $option) {
            $option = trim(strip_tags((string) $option));
            if ($option === '') {
                continue;
            }
            $html .= '<option value="' . $this->escape($option) . '">';
        }
        return $html . '</datalist>';
    }
}

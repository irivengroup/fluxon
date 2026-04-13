<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Presentation\Html\Escaper;

final class DatalistElement extends AbstractElement
{
    public function __construct(string $label, private readonly array $options = [], array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes->set('type', 'text');
        $this->attributes->set('list', 'datalist-' . $this->attributes->get('id'));
    }

    public function render(): string
    {
        $listId = (string) $this->attributes->get('list');
        $html = $this->renderLabel() . '<input' . $this->attributes->render('text') . '>';
        $html .= '<datalist id="' . Escaper::attr($listId) . '">';

        foreach ($this->options as $option) {
            $value = trim(strip_tags((string) $option));
            if ($value === '') {
                continue;
            }
            $html .= '<option value="' . Escaper::attr($value) . '"></option>';
        }

        return $html . '</datalist>';
    }
}

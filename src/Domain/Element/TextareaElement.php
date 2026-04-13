<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

final class TextareaElement extends AbstractElement
{
    public function __construct(string $label, array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes
            ->set('type', 'textarea')
            ->set('rows', $this->attributes->get('rows', 6))
            ->set('cols', $this->attributes->get('cols', 60))
            ->ignore('value');

        $style = trim((string) $this->label->attributes()->get('style', '') . ' vertical-align: top;');
        $this->label->attributes()->set('style', $style);
    }

    public function render(): string
    {
        return $this->renderLabel()
            . '<textarea' . $this->attributes->render() . '>'
            . $this->escape((string) $this->attributes->get('value', ''))
            . '</textarea>';
    }
}

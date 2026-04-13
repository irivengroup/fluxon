<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

class InputElement extends AbstractElement
{
    public function __construct(string $label, string $type = 'text', array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes->set('type', $type);
    }

    public function render(): string
    {
        $type = (string) $this->attributes->get('type', 'text');

        return $this->renderLabel()
            . '<input'
            . $this->attributes->render($type)
            . '>';
    }
}

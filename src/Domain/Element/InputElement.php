<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

class InputElement extends AbstractElement
{
    public function __construct(string $label, array $attributes = [], string $type = 'text')
    {
        parent::__construct($label, $attributes);
        $this->attributes->set('type', $type);
    }

    public function render(): string
    {
        return $this->renderLabel() . '<input' . $this->attributes->render() . '>';
    }
}

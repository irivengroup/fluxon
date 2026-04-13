<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Presentation\Html\Escaper;

final class TextareaElement extends AbstractElement
{
    public function __construct(string $label, array $attributes = [])
    {
        parent::__construct($label, $attributes);
        $this->attributes
            ->set('type', 'textarea')
            ->set('rows', $this->attributes->get('rows', 6))
            ->set('cols', $this->attributes->get('cols', 60));
    }

    public function render(): string
    {
        $value = (string) $this->attributes->get('value', '');
        $attrs = clone $this->attributes;
        $attrs->ignore(['value']);

        return $this->renderLabel()
            . '<textarea'
            . $attrs->render('textarea')
            . '>'
            . Escaper::text($value)
            . '</textarea>';
    }
}

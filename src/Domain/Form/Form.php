<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlAttributes;

final class Form implements Renderable
{
    private HtmlAttributes $attributes;
    /** @var list<Renderable|string> */
    private array $content = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = new HtmlAttributes($attributes);
        $this->attributes->set('type', 'form');

        if (!$this->attributes->has('name')) {
            $this->attributes->set('name', 'form-' . bin2hex(random_bytes(8)));
        }
        if (!$this->attributes->has('method')) {
            $this->attributes->set('method', 'post');
        }
        if (!$this->attributes->has('enctype')) {
            $this->attributes->set('enctype', 'application/x-www-form-urlencoded');
        }
        if (!$this->attributes->has('action')) {
            $this->attributes->set('action', '');
        }
    }

    public function attributes(): HtmlAttributes
    {
        return $this->attributes;
    }

    public function append(Renderable|string $node): self
    {
        $this->content[] = $node;

        return $this;
    }

    public function render(): string
    {
        $attributes = clone $this->attributes;
        if (strtolower((string) $attributes->get('method')) === 'get') {
            $attributes->remove('enctype');
        }

        $html = '<form' . $attributes->render('form') . '>';
        foreach ($this->content as $node) {
            $html .= is_string($node) ? $node : $node->render();
        }
        return $html . '</form>';
    }
}

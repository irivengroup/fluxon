<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

final class Form implements Renderable
{
    private Attributes $attributes;
    /** @var list<Renderable|string> */
    private array $children = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = new Attributes($attributes);
        $this->attributes
            ->set('name', (string) $this->attributes->get('name', 'form-' . bin2hex(random_bytes(8))))
            ->set('method', strtolower((string) $this->attributes->get('method', 'post')))
            ->set('action', (string) $this->attributes->get('action', ''));

        if (!$this->attributes->has('enctype')) {
            $this->attributes->set('enctype', 'application/x-www-form-urlencoded');
        }
    }

    public function attributes(): Attributes
    {
        return $this->attributes;
    }

    public function append(Renderable|string $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    public function render(): string
    {
        if (strtolower((string) $this->attributes->get('method')) === 'get') {
            $this->attributes->remove('enctype');
        }
        $html = '<form' . $this->attributes->render() . '>';
        foreach ($this->children as $child) {
            $html .= is_string($child) ? $child : $child->render();
        }
        return $html . '</form>';
    }
}

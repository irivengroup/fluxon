<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

final class Fieldset implements Renderable
{
    private Attributes $attributes;
    private ?string $legend;
    private array $legendAttributes;
    /** @var list<Renderable|string> */
    private array $children = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = new Attributes($attributes);
        $this->legend = isset($attributes['legend']) ? (string) $attributes['legend'] : null;
        $this->legendAttributes = is_array($attributes['legend-attributes'] ?? null) ? $attributes['legend-attributes'] : [];
        $this->attributes->ignore(['legend', 'legend-attributes']);
    }

    public function append(Renderable|string $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    public function render(): string
    {
        $html = '<fieldset' . $this->attributes->render() . '>';
        if ($this->legend !== null && $this->legend !== '') {
            $legendAttributes = new Attributes($this->legendAttributes);
            $html .= '<legend' . $legendAttributes->render() . '>'
                . htmlspecialchars($this->legend, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                . '</legend>';
        }
        foreach ($this->children as $child) {
            $html .= is_string($child) ? $child : $child->render();
        }
        return $html . '</fieldset>';
    }
}

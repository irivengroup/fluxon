<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Domain\Label;
use Iriven\PhpFormGenerator\Domain\ValueObject\Attributes;

abstract class AbstractElement implements Renderable
{
    protected Label $label;
    protected Attributes $attributes;

    public function __construct(string $label, array $attributes = [])
    {
        $this->label = new Label($label);
        $this->attributes = new Attributes($attributes);
        if (!$this->attributes->has('name')) {
            $this->attributes->set('name', $this->normalize($label));
        }
        if (!$this->attributes->has('id')) {
            $this->attributes->createElementId((string) $this->attributes->get('name'));
        }
    }

    public function attributes(): Attributes
    {
        return $this->attributes;
    }

    protected function renderLabel(): string
    {
        return $this->label->render((string) $this->attributes->get('type', 'text'), (string) $this->attributes->get('id'));
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    protected function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-_]+/', '-', $value) ?? $value;
        return trim($value, '-');
    }
}

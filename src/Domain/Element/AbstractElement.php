<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Presentation\Html\HtmlAttributes;
use Iriven\PhpFormGenerator\Presentation\Html\LabelView;
use Iriven\PhpFormGenerator\Presentation\Html\Str;

abstract class AbstractElement implements ElementInterface
{
    protected HtmlAttributes $attributes;
    protected LabelView $label;

    public function __construct(string $label, array $attributes = [])
    {
        $this->label = new LabelView($label, new HtmlAttributes());
        $this->attributes = new HtmlAttributes($attributes);

        if (!$this->attributes->has('name')) {
            $this->attributes->set('name', Str::normalizeName($label));
        }
        if (!$this->attributes->has('id')) {
            $this->attributes->createElementId((string) $this->attributes->get('name'));
        }
        if (!$this->attributes->has('autocomplete')) {
            $this->attributes->set('autocomplete', 'off');
        }
        if (!$this->attributes->has('type')) {
            $this->attributes->set('type', 'text');
        }
    }

    public function attributes(): HtmlAttributes
    {
        return $this->attributes;
    }

    public function name(): string
    {
        return (string) $this->attributes->get('name');
    }

    public function setValue(mixed $value): void
    {
        $this->attributes->set('value', $value);
    }

    public function shouldForceMultipart(): bool
    {
        return false;
    }

    protected function renderLabel(): string
    {
        return $this->label->renderFor(
            (string) $this->attributes->get('type', 'text'),
            (string) $this->attributes->get('id')
        );
    }
}

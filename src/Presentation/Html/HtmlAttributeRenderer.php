<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class HtmlAttributeRenderer
{
    /** @param array<string, mixed> $attributes */
    public function render(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            if ($this->shouldSkipAttribute($name, $value)) {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . $this->e((string) $name);
                continue;
            }

            $html .= ' ' . $this->e((string) $name) . '="' . $this->e((string) $value) . '"';
        }

        return $html;
    }

    private function shouldSkipAttribute(string $name, mixed $value): bool
    {
        return $name === 'choices'
            || $name === 'prototype_view'
            || $value === false
            || $value === null;
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

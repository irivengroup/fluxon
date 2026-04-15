<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Field\AbstractFieldType;
use Iriven\PhpFormGenerator\Domain\Form\FormView;

final class HtmlWidgetAttributeBuilder
{
    /** @return array<string, mixed> */
    public function build(FormView $view): array
    {
        $attr = $this->baseAttributes($view);
        $attr = $this->applyRequiredAttribute($attr, $view);
        $attr = $this->applyPlaceholderAttribute($attr, $view);
        $attr = $this->applyMultipleAttribute($attr, $view);
        $attr = $this->applyAccessibilityAttributes($attr, $view);

        return $attr;
    }

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
     */
    public function applyCaptchaAttributes(array $attr, FormView $view): array
    {
        $attr['inputmode'] = 'text';
        $attr['maxlength'] = (string) ($view->vars['max_length'] ?? 8);
        $attr['minlength'] = (string) ($view->vars['min_length'] ?? 5);
        $attr['autocomplete'] = 'off';
        $attr['autocapitalize'] = 'off';
        $attr['spellcheck'] = 'false';

        return $attr;
    }

    public function resolveHtmlType(string $typeClass): string
    {
        return class_exists($typeClass) && is_subclass_of($typeClass, AbstractFieldType::class)
            ? $typeClass::htmlType()
            : 'text';
    }

    /** @return array<string, mixed> */
    private function baseAttributes(FormView $view): array
    {
        $attr = is_array($view->vars['attr'] ?? null) ? $view->vars['attr'] : [];
        $attr['id'] = $view->id;
        $attr['name'] = $view->fullName;

        return $attr;
    }

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
     */
    private function applyRequiredAttribute(array $attr, FormView $view): array
    {
        if (($view->vars['required'] ?? false) === true) {
            $attr['required'] = 'required';
        }

        return $attr;
    }

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
     */
    private function applyPlaceholderAttribute(array $attr, FormView $view): array
    {
        if (($view->vars['placeholder'] ?? null) !== null) {
            $attr['placeholder'] = (string) $view->vars['placeholder'];
        }

        return $attr;
    }

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
     */
    private function applyMultipleAttribute(array $attr, FormView $view): array
    {
        if (($view->vars['multiple'] ?? false) === true) {
            $attr['multiple'] = 'multiple';
        }

        return $attr;
    }

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
     */
    private function applyAccessibilityAttributes(array $attr, FormView $view): array
    {
        if ($view->errors !== []) {
            $attr['aria-invalid'] = 'true';
        }

        $describedBy = [];
        if (($view->vars['help'] ?? null) !== null) {
            $describedBy[] = $view->id . '_help';
        }
        if ($view->errors !== []) {
            $describedBy[] = $view->id . '_errors';
        }
        if ($describedBy !== []) {
            $attr['aria-describedby'] = implode(' ', $describedBy);
        }

        return $attr;
    }
}

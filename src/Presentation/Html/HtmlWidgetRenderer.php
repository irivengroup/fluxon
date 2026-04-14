<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Field\AbstractFieldType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlWidgetRenderer
{
    public function __construct(private readonly ThemeInterface $theme)
    {
    }

    public function render(FormView $view): string
    {
        $typeClass = (string) ($view->vars['type_class'] ?? $view->type);
        $attr = $this->baseAttributes($view);
        $htmlType = $this->resolveHtmlType($typeClass);

        if ($htmlType === 'captcha') {
            $htmlType = 'text';
            $attr = $this->applyCaptchaAttributes($attr, $view);
        }

        return match ($htmlType) {
            'textarea' => $this->renderTextareaWidget($view, $attr),
            'select' => $this->renderSelectWidget($view, $typeClass, $attr),
            'radio' => $this->renderRadioWidget($view, $attr),
            'datalist' => $this->renderDatalistWidget($view, $attr),
            'button' => $this->renderButtonWidget($view, $attr),
            default => $this->renderInputWidget($view, $htmlType, $attr),
        };
    }

    /** @return array<string, mixed> */
    private function baseAttributes(FormView $view): array
    {
        $attr = is_array($view->vars['attr'] ?? null) ? $view->vars['attr'] : [];
        $attr['id'] = $view->id;
        $attr['name'] = $view->fullName;

        if (($view->vars['required'] ?? false) === true) {
            $attr['required'] = 'required';
        }
        if (($view->vars['placeholder'] ?? null) !== null) {
            $attr['placeholder'] = (string) $view->vars['placeholder'];
        }
        if (($view->vars['multiple'] ?? false) === true) {
            $attr['multiple'] = 'multiple';
        }

        return $attr;
    }

    private function resolveHtmlType(string $typeClass): string
    {
        return class_exists($typeClass) && is_subclass_of($typeClass, AbstractFieldType::class)
            ? $typeClass::htmlType()
            : 'text';
    }

    /** @param array<string, mixed> $attr
     *  @return array<string, mixed>
     */
    private function applyCaptchaAttributes(array $attr, FormView $view): array
    {
        $attr['inputmode'] = 'text';
        $attr['maxlength'] = (string) ($view->vars['max_length'] ?? 8);
        $attr['minlength'] = (string) ($view->vars['min_length'] ?? 5);
        $attr['autocomplete'] = 'off';
        $attr['autocapitalize'] = 'off';
        $attr['spellcheck'] = 'false';

        return $attr;
    }

    /** @param array<string, mixed> $attr */
    private function renderTextareaWidget(FormView $view, array $attr): string
    {
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<textarea' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->value ?? '')) . '</textarea>';
    }

    /** @param array<string, mixed> $attr */
    private function renderSelectWidget(FormView $view, string $typeClass, array $attr): string
    {
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());
        $choices = $this->resolveSelectChoices($view, $typeClass);
        $multiple = ($view->vars['multiple'] ?? false) === true;

        if ($multiple) {
            $attr['name'] = $view->fullName . '[]';
        }

        $selectedValues = $multiple && is_array($view->value)
            ? array_map('strval', $view->value)
            : [(string) $view->value];

        $html = '<select' . $this->renderAttributes($attr) . '>';
        $html .= $this->renderSelectPlaceholder($view);

        foreach ($choices as $choiceValue => $label) {
            $selected = in_array((string) $choiceValue, $selectedValues, true) ? ' selected' : '';
            $html .= '<option value="' . $this->e((string) $choiceValue) . '"' . $selected . '>' . $this->e((string) $label) . '</option>';
        }

        return $html . '</select>';
    }

    /** @return array<string, string> */
    private function resolveSelectChoices(FormView $view, string $typeClass): array
    {
        $choices = $view->vars['choices'] ?? null;
        if (is_array($choices)) {
            /** @var array<string, string> $choices */
            return $choices;
        }
        if ($typeClass === CountryType::class) {
            return CountryType::choices($view->vars);
        }
        if ($typeClass === YesNoType::class) {
            return YesNoType::choices();
        }

        return [];
    }

    private function renderSelectPlaceholder(FormView $view): string
    {
        if (!isset($view->vars['placeholder']) || !is_string($view->vars['placeholder']) || $view->vars['placeholder'] === '') {
            return '';
        }

        $selected = ((string) $view->value === '') ? ' selected' : '';

        return '<option value=""' . $selected . '>' . $this->e($view->vars['placeholder']) . '</option>';
    }

    /** @param array<string, mixed> $attr */
    private function renderRadioWidget(FormView $view, array $attr): string
    {
        $choices = is_array($view->vars['choices'] ?? null) ? $view->vars['choices'] : [];
        $html = '';

        foreach ($choices as $choiceValue => $label) {
            $radioAttr = $attr;
            $radioAttr['type'] = 'radio';
            $radioAttr['value'] = (string) $choiceValue;
            if ((string) $view->value === (string) $choiceValue) {
                $radioAttr['checked'] = 'checked';
            }
            $html .= '<label class="' . $this->e($this->theme->labelClass()) . '"><input' . $this->renderAttributes($radioAttr) . '> ' . $this->e((string) $label) . '</label>';
        }

        return $html;
    }

    /** @param array<string, mixed> $attr */
    private function renderDatalistWidget(FormView $view, array $attr): string
    {
        $listId = $view->id . '_list';
        $attr['type'] = 'text';
        $attr['list'] = $listId;
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        $html = '<input' . $this->renderAttributes($attr + ['value' => (string) ($view->value ?? '')]) . '>';
        $html .= '<datalist id="' . $this->e($listId) . '">';

        $choices = is_array($view->vars['choices'] ?? null) ? $view->vars['choices'] : [];
        foreach ($choices as $option) {
            $html .= '<option value="' . $this->e((string) $option) . '"></option>';
        }

        return $html . '</datalist>';
    }

    /** @param array<string, mixed> $attr */
    private function renderButtonWidget(FormView $view, array $attr): string
    {
        $attr['type'] = $view->vars['button_type'] ?? 'button';
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<button' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</button>';
    }

    /** @param array<string, mixed> $attr */
    private function renderInputWidget(FormView $view, string $htmlType, array $attr): string
    {
        $attr['type'] = $htmlType;
        if ($htmlType !== 'file' && $htmlType !== 'checkbox') {
            $attr['value'] = is_scalar($view->value) ? (string) $view->value : '';
        }
        if ($htmlType === 'checkbox') {
            $attr['value'] = '1';
            if ((bool) $view->value === true) {
                $attr['checked'] = 'checked';
            }
        }
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<input' . $this->renderAttributes($attr) . '>';
    }

    /** @param array<string, mixed> $attributes */
    private function renderAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if ($name === 'choices' || $name === 'prototype_view' || $value === false || $value === null) {
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

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Field\AbstractFieldType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\Fieldset;
use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\DefaultTheme;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlRenderer
{
    public function __construct(private readonly ThemeInterface $theme = new DefaultTheme())
    {
    }

    public function renderForm(FormView $view): string
    {
        $method = $this->e((string) ($view->vars['method'] ?? 'POST'));
        $action = $this->e((string) ($view->vars['action'] ?? ''));
        $attr = $this->renderAttributes($view->vars['attr'] ?? []);

        $html = sprintf('<form method="%s" action="%s" class="%s"%s>', $method, $action, $this->e($this->theme->formClass()), $attr);

        if ($view->errors !== []) {
            $html .= $this->renderErrors($view->errors);
        }

        /** @var array<string, FormView> $grouped */
        $grouped = [];
        foreach ($view->children as $child) {
            $grouped[$child->name] = $child;
        }

        /** @var array<string, bool> $used */
        $used = [];
        foreach ($view->fieldsets as $fieldset) {
            $html .= $this->renderFieldset($fieldset, $grouped, $used);
        }

        foreach ($view->children as $child) {
            if (!isset($used[$child->name])) {
                $html .= $this->renderRow($child);
            }
        }

        return $html . '</form>';
    }

    /**
     * @param array<string, FormView> $grouped
     * @param array<string, bool> $used
     */
    private function renderFieldset(Fieldset $fieldset, array $grouped, array &$used): string
    {
        $html = '<fieldset class="' . $this->e($this->theme->fieldsetClass()) . '">';
        if (($fieldset->options['legend'] ?? null) !== null) {
            $html .= '<legend>' . $this->e((string) $fieldset->options['legend']) . '</legend>';
        }
        if (($fieldset->options['description'] ?? null) !== null) {
            $html .= '<p>' . $this->e((string) $fieldset->options['description']) . '</p>';
        }
        foreach ($fieldset->fields as $name) {
            if (isset($grouped[$name])) {
                $used[$name] = true;
                $html .= $this->renderRow($grouped[$name]);
            }
        }
        foreach ($fieldset->children as $childFieldset) {
            $html .= $this->renderFieldset($childFieldset, $grouped, $used);
        }

        return $html . '</fieldset>';
    }

    public function renderRow(FormView $view): string
    {
        if ($view->type === 'compound') {
            return $this->renderCompoundRow($view);
        }

        if ($view->type === 'collection') {
            return $this->renderCollectionRow($view);
        }

        if (($view->vars['type_class'] ?? '') === 'hidden' || $view->type === 'hidden') {
            return $this->renderWidget($view);
        }

        $html = '<div class="' . $this->e($this->theme->rowClass()) . '">';
        if ($view->type !== 'Iriven\\PhpFormGenerator\\Domain\\Field\\CheckboxType') {
            $html .= $this->renderLabel($view);
        }
        $html .= $this->renderWidget($view);
        if ($view->type === 'Iriven\\PhpFormGenerator\\Domain\\Field\\CheckboxType') {
            $html .= $this->renderLabel($view);
        }
        if ($view->errors !== []) {
            $html .= $this->renderErrors($view->errors);
        }
        if (($view->vars['help'] ?? null) !== null) {
            $html .= '<small>' . $this->e((string) $view->vars['help']) . '</small>';
        }

        return $html . '</div>';
    }

    private function renderCompoundRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '"><fieldset>';
        $html .= '<legend>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</legend>';

        foreach ($view->children as $child) {
            $html .= $this->renderRow($child);
        }

        if ($view->errors !== []) {
            $html .= $this->renderErrors($view->errors);
        }

        return $html . '</fieldset></div>';
    }

    private function renderCollectionRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '" data-collection="1">';
        $html .= $this->renderLabel($view);

        foreach ($view->children as $child) {
            $html .= '<div data-collection-entry="1">';
            foreach ($child->children as $grandChild) {
                $html .= $this->renderRow($grandChild);
            }
            $html .= '</div>';
        }

        if (isset($view->vars['prototype_view']) && $view->vars['prototype_view'] instanceof FormView) {
            $prototype = '';
            foreach ($view->vars['prototype_view']->children as $grandChild) {
                $prototype .= $this->renderRow($grandChild);
            }
            $html .= '<template data-prototype="1">' . htmlspecialchars($prototype, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</template>';
        }

        if ($view->errors !== []) {
            $html .= $this->renderErrors($view->errors);
        }

        return $html . '</div>';
    }

    private function renderLabel(FormView $view): string
    {
        return sprintf(
            '<label for="%s" class="%s">%s</label>',
            $this->e($view->id),
            $this->e($this->theme->labelClass()),
            $this->e((string) ($view->vars['label'] ?? $view->name))
        );
    }

    private function renderWidget(FormView $view): string
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param array<string, mixed> $attr
     * @return array<string, mixed>
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

    /**
     * @param array<string, mixed> $attr
     */
    private function renderTextareaWidget(FormView $view, array $attr): string
    {
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<textarea' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->value ?? '')) . '</textarea>';
    }

    /**
     * @param array<string, mixed> $attr
     */
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

    /**
     * @return array<string, string>
     */
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

    /**
     * @param array<string, mixed> $attr
     */
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

    /**
     * @param array<string, mixed> $attr
     */
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

    /**
     * @param array<string, mixed> $attr
     */
    private function renderButtonWidget(FormView $view, array $attr): string
    {
        $attr['type'] = $view->vars['button_type'] ?? 'button';
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<button' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</button>';
    }

    /**
     * @param array<string, mixed> $attr
     */
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

    /** @param array<int, string> $errors */
    private function renderErrors(array $errors): string
    {
        $html = '';
        foreach ($errors as $error) {
            $html .= '<div class="' . $this->e($this->theme->errorClass()) . '">' . $this->e($error) . '</div>';
        }

        return $html;
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

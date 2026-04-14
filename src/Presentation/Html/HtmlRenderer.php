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

        $grouped = [];
        foreach ($view->children as $child) {
            $grouped[$child->name] = $child;
        }

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

    /** @param array<string, FormView> $grouped @param array<string, bool> $used */
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

        if ($view->type === 'collection') {
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
        $attr = $view->vars['attr'] ?? [];
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

        $htmlType = class_exists($typeClass) && is_subclass_of($typeClass, AbstractFieldType::class)
            ? $typeClass::htmlType()
            : 'text';

        if ($htmlType === 'textarea') {
            $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());
            return '<textarea' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->value ?? '')) . '</textarea>';
        }

        if ($htmlType === 'select') {
            $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());
            $choices = $view->vars['choices'] ?? ($typeClass === CountryType::class ? CountryType::choices() : ($typeClass === YesNoType::class ? YesNoType::choices() : []));
            $multiple = ($view->vars['multiple'] ?? false) === true;
            if ($multiple) {
                $attr['name'] = $view->fullName . '[]';
            }

            $selectedValues = $multiple && is_array($view->value) ? array_map('strval', $view->value) : [(string) $view->value];
            $html = '<select' . $this->renderAttributes($attr) . '>';
            foreach ($choices as $choiceValue => $label) {
                $selected = in_array((string) $choiceValue, $selectedValues, true) ? ' selected' : '';
                $html .= '<option value="' . $this->e((string) $choiceValue) . '"' . $selected . '>' . $this->e((string) $label) . '</option>';
            }
            return $html . '</select>';
        }

        if ($htmlType === 'radio') {
            $choices = $view->vars['choices'] ?? [];
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

        if ($htmlType === 'datalist') {
            $listId = $view->id . '_list';
            $attr['type'] = 'text';
            $attr['list'] = $listId;
            $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());
            $html = '<input' . $this->renderAttributes($attr + ['value' => (string) ($view->value ?? '')]) . '>';
            $html .= '<datalist id="' . $this->e($listId) . '">';
            foreach (($view->vars['choices'] ?? []) as $option) {
                $html .= '<option value="' . $this->e((string) $option) . '"></option>';
            }
            return $html . '</datalist>';
        }

        if ($htmlType === 'button') {
            $attr['type'] = $view->vars['button_type'] ?? 'button';
            $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());
            return '<button' . $this->renderAttributes($attr) . '>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</button>';
        }

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

    /** @param list<string> $errors */
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

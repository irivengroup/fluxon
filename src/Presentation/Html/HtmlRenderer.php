<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Contract\ThemeInterface;
use Iriven\PhpFormGenerator\Domain\Form\FieldDefinition;
use Iriven\PhpFormGenerator\Domain\Form\Fieldset;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\DefaultTheme;

final class HtmlRenderer
{
    public function __construct(private readonly ?ThemeInterface $theme = null)
    {
    }

    public function render(Form $form): string
    {
        $theme = $this->theme ?? new DefaultTheme();
        $method = $this->escape((string) ($form->options()['method'] ?? 'POST'));
        $action = $this->escape((string) ($form->options()['action'] ?? ''));
        $enctype = $this->needsMultipart($form) ? ' enctype="multipart/form-data"' : '';
        $html = '<form class="' . $this->escape($theme->formClass()) . '" method="' . $method . '" action="' . $action . '"' . $enctype . '>';

        $token = $form->csrfToken();
        if ($token !== null) {
            $field = $this->escape((string) ($form->options()['csrf_field_name'] ?? '_token'));
            $html .= '<input type="hidden" name="' . $field . '" value="' . $this->escape($token) . '">';
        }

        foreach ($form->errors() as $error) {
            $html .= '<div class="' . $this->escape($theme->errorClass()) . '">' . $this->escape($error) . '</div>';
        }

        foreach ($form->fieldsets() as $fieldset) {
            $html .= $this->renderFieldset($fieldset, $theme);
        }

        $renderedInsideFieldsets = [];
        foreach ($form->fieldsets() as $fieldset) {
            foreach ($this->collectFieldNames($fieldset) as $name => $_) {
                $renderedInsideFieldsets[$name] = true;
            }
        }

        foreach ($form->fields() as $field) {
            if (!isset($renderedInsideFieldsets[$field->name])) {
                $html .= $this->renderField($field, $theme);
            }
        }

        return $html . '</form>';
    }

    private function renderFieldset(Fieldset $fieldset, ThemeInterface $theme): string
    {
        $legend = $fieldset->options()['legend'] ?? null;
        $description = $fieldset->options()['description'] ?? null;
        $attr = $fieldset->options()['attr'] ?? [];
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $theme->fieldsetClass());

        $html = '<fieldset' . $this->attributes($attr) . '>';
        if (is_string($legend) && $legend !== '') {
            $html .= '<legend>' . $this->escape($legend) . '</legend>';
        }
        if (is_string($description) && $description !== '') {
            $html .= '<p class="' . $this->escape($theme->helpClass()) . '">' . $this->escape($description) . '</p>';
        }
        foreach ($fieldset->fields() as $field) {
            $html .= $this->renderField($field, $theme);
        }
        foreach ($fieldset->children() as $child) {
            $html .= $this->renderFieldset($child, $theme);
        }
        return $html . '</fieldset>';
    }

    private function renderField(FieldDefinition $field, ThemeInterface $theme): string
    {
        $type = $field->type->renderType();
        $name = $this->escape($field->name);
        $id = $this->escape($field->id());
        $label = $this->escape($field->label());

        $rowAttr = (array) ($field->options['row_attr'] ?? []);
        $rowAttr['class'] = trim(($rowAttr['class'] ?? '') . ' ' . $theme->rowClass());

        $attr = (array) ($field->options['attr'] ?? []);
        $attr['id'] = $field->id();
        $attr['name'] = $field->name;
        if ($type !== 'textarea' && $type !== 'select' && $type !== 'submit') {
            $attr['value'] = is_array($field->value) ? '' : (string) ($field->value ?? '');
        }
        if ($type !== 'submit' && $type !== 'hidden' && !isset($attr['class'])) {
            $attr['class'] = $theme->inputClass();
        } elseif ($type !== 'submit' && $type !== 'hidden') {
            $attr['class'] = trim($attr['class'] . ' ' . $theme->inputClass());
        }

        $html = '<div' . $this->attributes($rowAttr) . '>';
        if ($type !== 'submit' && $type !== 'hidden') {
            $labelAttr = (array) ($field->options['label_attr'] ?? []);
            $labelAttr['for'] = $field->id();
            $labelAttr['class'] = trim(($labelAttr['class'] ?? '') . ' ' . $theme->labelClass());
            $html .= '<label' . $this->attributes($labelAttr) . '>' . $label . '</label>';
        }

        if ($type === 'submit') {
            $buttonAttr = $attr;
            $buttonAttr['type'] = 'submit';
            unset($buttonAttr['value']);
            $html .= '<button' . $this->attributes($buttonAttr) . '>' . $label . '</button>';
        } elseif ($type === 'textarea') {
            unset($attr['value']);
            $html .= '<textarea' . $this->attributes($attr) . '>' . $this->escape((string) ($field->value ?? '')) . '</textarea>';
        } elseif ($type === 'select') {
            unset($attr['value']);
            $html .= '<select' . $this->attributes($attr) . '>';
            $placeholder = $field->options['placeholder'] ?? null;
            if (is_string($placeholder) && $placeholder !== '') {
                $html .= '<option value="">' . $this->escape($placeholder) . '</option>';
            }
            foreach ((array) ($field->options['choices'] ?? []) as $choiceLabel => $choiceValue) {
                $selected = (string) $choiceValue === (string) ($field->value ?? '') ? ' selected' : '';
                $html .= '<option value="' . $this->escape((string) $choiceValue) . '"' . $selected . '>' . $this->escape((string) $choiceLabel) . '</option>';
            }
            $html .= '</select>';
        } elseif ($type === 'checkbox') {
            if ((string) ($field->value ?? '0') === (string) ($field->options['checked_value'] ?? '1')) {
                $attr['checked'] = 'checked';
            }
            $attr['type'] = 'checkbox';
            $attr['value'] = (string) ($field->options['checked_value'] ?? '1');
            $html .= '<input' . $this->attributes($attr) . '>';
        } else {
            $attr['type'] = $type;
            $html .= '<input' . $this->attributes($attr) . '>';
        }

        if (is_string($field->options['help'] ?? null) && $field->options['help'] !== '') {
            $html .= '<div class="' . $this->escape($theme->helpClass()) . '">' . $this->escape((string) $field->options['help']) . '</div>';
        }

        foreach ($field->errors as $error) {
            $html .= '<div class="' . $this->escape($theme->errorClass()) . '">' . $this->escape($error) . '</div>';
        }
        return $html . '</div>';
    }

    /** @return array<string,true> */
    private function collectFieldNames(Fieldset $fieldset): array
    {
        $names = [];
        foreach ($fieldset->fields() as $field) {
            $names[$field->name] = true;
        }
        foreach ($fieldset->children() as $child) {
            foreach ($this->collectFieldNames($child) as $name => $_) {
                $names[$name] = true;
            }
        }
        return $names;
    }

    private function needsMultipart(Form $form): bool
    {
        foreach ($form->fields() as $field) {
            if ($field->type->renderType() === 'file') {
                return true;
            }
        }
        return false;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /** @param array<string,mixed> $attributes */
    private function attributes(array $attributes): string
    {
        $compiled = '';
        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            if ($value === true) {
                $compiled .= ' ' . $this->escape((string) $name);
                continue;
            }
            $compiled .= ' ' . $this->escape((string) $name) . '="' . $this->escape((string) $value) . '"';
        }
        return $compiled;
    }
}

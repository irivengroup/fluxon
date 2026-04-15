<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlSimpleWidgetRenderer
{
    private HtmlAttributeRenderer $attributeRenderer;

    public function __construct(private readonly ThemeInterface $theme)
    {
        $this->attributeRenderer = new HtmlAttributeRenderer();
    }

    /**
     * @param array<string, mixed> $attr
     */
    public function renderTextarea(FormView $view, array $attr): string
    {
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<textarea' . $this->attributeRenderer->render($attr) . '>' . $this->e((string) ($view->value ?? '')) . '</textarea>';
    }

    /**
     * @param array<string, mixed> $attr
     */
    public function renderRadio(FormView $view, array $attr): string
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
            $html .= '<label class="' . $this->e($this->theme->labelClass()) . '"><input' . $this->attributeRenderer->render($radioAttr) . '> ' . $this->e((string) $label) . '</label>';
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $attr
     */
    public function renderDatalist(FormView $view, array $attr): string
    {
        $listId = $view->id . '_list';
        $attr['type'] = 'text';
        $attr['list'] = $listId;
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        $html = '<input' . $this->attributeRenderer->render($attr + ['value' => (string) ($view->value ?? '')]) . '>';
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
    public function renderButton(FormView $view, array $attr): string
    {
        $attr['type'] = $view->vars['button_type'] ?? 'button';
        $attr['class'] = trim(($attr['class'] ?? '') . ' ' . $this->theme->inputClass());

        return '<button' . $this->attributeRenderer->render($attr) . '>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</button>';
    }

    /**
     * @param array<string, mixed> $attr
     */
    public function renderInput(FormView $view, string $htmlType, array $attr): string
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

        return '<input' . $this->attributeRenderer->render($attr) . '>';
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

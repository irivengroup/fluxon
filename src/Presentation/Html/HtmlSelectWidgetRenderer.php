<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlSelectWidgetRenderer
{
    private HtmlAttributeRenderer $attributeRenderer;

    public function __construct(private readonly ThemeInterface $theme)
    {
        $this->attributeRenderer = new HtmlAttributeRenderer();
    }

    /**
     * @param array<string, mixed> $attr
     */
    public function render(FormView $view, string $typeClass, array $attr): string
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

        $html = '<select' . $this->attributeRenderer->render($attr) . '>';
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

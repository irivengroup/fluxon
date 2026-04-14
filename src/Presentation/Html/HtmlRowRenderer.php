<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlRowRenderer
{
    public function __construct(
        private readonly ThemeInterface $theme,
        private readonly HtmlWidgetRenderer $widgetRenderer,
    ) {
    }

    public function render(FormView $view): string
    {
        if ($view->type === 'compound') {
            return $this->renderCompoundRow($view);
        }
        if ($view->type === 'collection') {
            return $this->renderCollectionRow($view);
        }
        if (($view->vars['type_class'] ?? '') === 'hidden' || $view->type === 'hidden') {
            return $this->widgetRenderer->render($view);
        }

        $html = '<div class="' . $this->e($this->theme->rowClass()) . '">';
        if ($view->type !== 'Iriven\\PhpFormGenerator\\Domain\\Field\\CheckboxType') {
            $html .= $this->renderLabel($view);
        }
        $html .= $this->widgetRenderer->render($view);
        if ($view->type === 'Iriven\\PhpFormGenerator\\Domain\\Field\\CheckboxType') {
            $html .= $this->renderLabel($view);
        }
        $html .= $this->renderErrors($view->errors);
        $html .= $this->renderHelp($view);

        return $html . '</div>';
    }

    private function renderCompoundRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '"><fieldset>';
        $html .= '<legend>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</legend>';
        foreach ($view->children as $child) {
            $html .= $this->render($child);
        }
        $html .= $this->renderErrors($view->errors);

        return $html . '</fieldset></div>';
    }

    private function renderCollectionRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '" data-collection="1">';
        $html .= $this->renderLabel($view);
        foreach ($view->children as $child) {
            $html .= '<div data-collection-entry="1">';
            foreach ($child->children as $grandChild) {
                $html .= $this->render($grandChild);
            }
            $html .= '</div>';
        }
        if (isset($view->vars['prototype_view']) && $view->vars['prototype_view'] instanceof FormView) {
            $prototype = '';
            foreach ($view->vars['prototype_view']->children as $grandChild) {
                $prototype .= $this->render($grandChild);
            }
            $html .= '<template data-prototype="1">' . htmlspecialchars($prototype, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</template>';
        }
        $html .= $this->renderErrors($view->errors);

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

    /** @param array<int, string> $errors */
    private function renderErrors(array $errors): string
    {
        $html = '';
        foreach ($errors as $error) {
            $html .= '<div class="' . $this->e($this->theme->errorClass()) . '">' . $this->e($error) . '</div>';
        }

        return $html;
    }

    private function renderHelp(FormView $view): string
    {
        if (($view->vars['help'] ?? null) === null) {
            return '';
        }

        return '<small>' . $this->e((string) $view->vars['help']) . '</small>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

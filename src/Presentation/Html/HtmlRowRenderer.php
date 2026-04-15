<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
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

        if ($this->isHiddenView($view)) {
            return $this->widgetRenderer->render($view);
        }

        return $this->renderSimpleRow($view);
    }

    private function renderCompoundRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '"><fieldset>';
        $html .= '<legend>' . $this->e((string) ($view->vars['label'] ?? $view->name)) . '</legend>';
        foreach ($view->children as $child) {
            $html .= $this->render($child);
        }
        $html .= $this->renderErrorsForView($view);

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
            $html .= $this->renderCollectionPrototype($view->vars['prototype_view']);
        }
        $html .= $this->renderErrorsForView($view);

        return $html . '</div>';
    }

    private function renderSimpleRow(FormView $view): string
    {
        $html = '<div class="' . $this->e($this->theme->rowClass()) . '">';
        $html .= $this->renderPreWidgetLabel($view);
        $html .= $this->widgetRenderer->render($view);
        $html .= $this->renderPostWidgetLabel($view);
        $html .= $this->renderErrorsForView($view);
        $html .= $this->renderHelp($view);

        return $html . '</div>';
    }

    private function isHiddenView(FormView $view): bool
    {
        return ($view->vars['type_class'] ?? '') === 'hidden'
            || $view->type === 'hidden'
            || ($view->vars['type_class'] ?? null) === HiddenType::class
            || $view->type === HiddenType::class;
    }

    private function isCheckboxView(FormView $view): bool
    {
        return $view->type === 'Iriven\PhpFormGenerator\Domain\Field\CheckboxType';
    }

    private function renderPreWidgetLabel(FormView $view): string
    {
        return $this->isCheckboxView($view) ? '' : $this->renderLabel($view);
    }

    private function renderPostWidgetLabel(FormView $view): string
    {
        return $this->isCheckboxView($view) ? $this->renderLabel($view) : '';
    }

    private function renderCollectionPrototype(FormView $prototypeView): string
    {
        $prototype = '';
        foreach ($prototypeView->children as $grandChild) {
            $prototype .= $this->render($grandChild);
        }

        return '<template data-prototype="1">' . htmlspecialchars($prototype, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</template>';
    }

    private function renderLabel(FormView $view): string
    {
        $required = (($view->vars['required'] ?? false) === true) ? ' <span aria-hidden="true">*</span>' : '';

        return sprintf(
            '<label for="%s" class="%s">%s%s</label>',
            $this->e($view->id),
            $this->e($this->theme->labelClass()),
            $this->e((string) ($view->vars['label'] ?? $view->name)),
            $required
        );
    }

    private function renderErrorsForView(FormView $view): string
    {
        return $this->renderErrors($view->errors, $view->id . '_errors');
    }

    /** @param array<int, string> $errors */
    private function renderErrors(array $errors, ?string $id = null): string
    {
        $html = '';
        foreach ($errors as $error) {
            $html .= '<div' . ($id !== null ? ' id="' . $this->e($id) . '"' : '') . ' role="alert" aria-live="polite" class="' . $this->e($this->theme->errorClass()) . '">' . $this->e($error) . '</div>';
        }

        return $html;
    }

    private function renderHelp(FormView $view): string
    {
        if (($view->vars['help'] ?? null) === null) {
            return '';
        }

        return '<small id="' . $this->e($view->id . '_help') . '">' . $this->e((string) $view->vars['help']) . '</small>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

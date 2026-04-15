<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Support\HtmlAttributeRenderer;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\DefaultTheme;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlRenderer
{
    private HtmlAttributeRenderer $attributeRenderer;
    private HtmlWidgetRenderer $widgetRenderer;
    private HtmlRowRenderer $rowRenderer;
    private HtmlFieldsetRenderer $fieldsetRenderer;

    public function __construct(private readonly ThemeInterface $theme = new DefaultTheme())
    {
        $this->attributeRenderer = new HtmlAttributeRenderer();
        $this->widgetRenderer = new HtmlWidgetRenderer($this->theme);
        $this->rowRenderer = new HtmlRowRenderer($this->theme, $this->widgetRenderer);
        $this->fieldsetRenderer = new HtmlFieldsetRenderer($this->theme, $this->rowRenderer);
    }

    public function renderForm(FormView $view): string
    {
        $html = $this->renderOpeningTag($view);
        $html .= $this->renderErrors($view->errors);

        $grouped = $this->groupChildrenByName($view);
        $used = [];
        $html .= $this->renderFieldsets($view, $grouped, $used);
        $html .= $this->renderUngroupedRows($view, $used);

        return $html . '</form>';
    }

    public function renderRow(FormView $view): string
    {
        return $this->rowRenderer->render($view);
    }

    public function renderWidget(FormView $view): string
    {
        return $this->widgetRenderer->render($view);
    }

    private function renderOpeningTag(FormView $view): string
    {
        $method = $this->e((string) ($view->vars['method'] ?? 'POST'));
        $action = $this->e((string) ($view->vars['action'] ?? ''));
        $attr = $this->attributeRenderer->render(is_array($view->vars['attr'] ?? null) ? $view->vars['attr'] : []);

        return sprintf(
            '<form method="%s" action="%s" class="%s"%s>',
            $method,
            $action,
            $this->e($this->theme->formClass()),
            $attr
        );
    }

    /**
     * @return array<string, FormView>
     */
    private function groupChildrenByName(FormView $view): array
    {
        $grouped = [];
        foreach ($view->children as $child) {
            $grouped[$child->name] = $child;
        }

        return $grouped;
    }

    /**
     * @param array<string, FormView> $grouped
     * @param array<string, bool> $used
     */
    private function renderFieldsets(FormView $view, array $grouped, array &$used): string
    {
        $html = '';
        foreach ($view->fieldsets as $fieldset) {
            $html .= $this->fieldsetRenderer->render($fieldset, $grouped, $used);
        }

        return $html;
    }

    /**
     * @param array<string, bool> $used
     */
    private function renderUngroupedRows(FormView $view, array $used): string
    {
        $html = '';
        foreach ($view->children as $child) {
            if (isset($used[$child->name])) {
                continue;
            }

            $html .= $this->rowRenderer->render($child);
        }

        return $html;
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

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

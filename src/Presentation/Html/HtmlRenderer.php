<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\FormView;
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
        $method = $this->e((string) ($view->vars['method'] ?? 'POST'));
        $action = $this->e((string) ($view->vars['action'] ?? ''));
        $attr = $this->attributeRenderer->render(is_array($view->vars['attr'] ?? null) ? $view->vars['attr'] : []);

        $html = sprintf('<form method="%s" action="%s" class="%s"%s>', $method, $action, $this->e($this->theme->formClass()), $attr);
        $html .= $this->renderErrors($view->errors);

        /** @var array<string, FormView> $grouped */
        $grouped = [];
        foreach ($view->children as $child) {
            $grouped[$child->name] = $child;
        }

        /** @var array<string, bool> $used */
        $used = [];
        foreach ($view->fieldsets as $fieldset) {
            $html .= $this->fieldsetRenderer->render($fieldset, $grouped, $used);
        }

        foreach ($view->children as $child) {
            if (!isset($used[$child->name])) {
                $html .= $this->rowRenderer->render($child);
            }
        }

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

    /** @param array<int, string> $errors */
    private function renderErrors(array $errors): string
    {
        $html = '';
        foreach ($errors as $error) {
            $html .= '<div class="' . $this->e($this->theme->errorClass()) . '">' . $this->e($error) . '</div>';
        }

        return $html;
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

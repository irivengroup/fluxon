<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\Fieldset;
use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class HtmlFieldsetRenderer
{
    public function __construct(
        private readonly ThemeInterface $theme,
        private readonly HtmlRowRenderer $rowRenderer,
    ) {
    }

    /**
     * @param array<string, FormView> $grouped
     * @param array<string, bool> $used
     */
    public function render(Fieldset $fieldset, array $grouped, array &$used): string
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
                $html .= $this->rowRenderer->render($grouped[$name]);
            }
        }
        foreach ($fieldset->children as $childFieldset) {
            $html .= $this->render($childFieldset, $grouped, $used);
        }

        return $html . '</fieldset>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

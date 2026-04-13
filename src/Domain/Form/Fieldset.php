<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Presentation\Html\Escaper;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlAttributes;

final class Fieldset implements Renderable
{
    public function __construct(private readonly array $attributes = [])
    {
    }

    public function renderOpen(): string
    {
        $attributes = new HtmlAttributes($this->attributes);
        $legend = $attributes->get('legend');
        $legendAttributes = is_array($attributes->get('legend-attributes')) ? new HtmlAttributes($attributes->get('legend-attributes')) : null;
        $attributes->ignore(['legend', 'legend-attributes']);

        $html = '<fieldset' . $attributes->render('fieldset') . '>';
        if ($legend !== null && $legend !== '') {
            $html .= '<legend';
            if ($legendAttributes !== null) {
                $html .= $legendAttributes->render('label');
            }
            $html .= '>' . Escaper::text((string) $legend) . '</legend>';
        }

        return $html;
    }

    public function renderClose(): string
    {
        return '</fieldset>';
    }

    public function render(): string
    {
        return $this->renderOpen() . $this->renderClose();
    }
}

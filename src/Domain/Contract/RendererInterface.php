<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Form\FormView;

interface RendererInterface
{
    public function renderForm(FormView $view): string;
    public function renderRow(FormView $view): string;
    public function renderWidget(FormView $view): string;
    public function renderLabel(FormView $view): string;
    public function renderErrors(FormView $view): string;
}

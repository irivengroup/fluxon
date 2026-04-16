<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures\Theme;

use Iriven\PhpFormGenerator\Presentation\Html\Theme\ThemeInterface;

final class MinimalTheme implements ThemeInterface
{
    public function formClass(): string { return 'minimal-form'; }
    public function rowClass(): string { return 'minimal-row'; }
    public function labelClass(): string { return 'minimal-label'; }
    public function inputClass(): string { return 'minimal-input'; }
    public function errorClass(): string { return 'minimal-error'; }
    public function helpClass(): string { return 'minimal-help'; }
    public function fieldsetClass(): string { return 'minimal-fieldset'; }
    public function legendClass(): string { return 'minimal-legend'; }
}

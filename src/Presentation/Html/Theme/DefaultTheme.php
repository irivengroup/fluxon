<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html\Theme;

use Iriven\PhpFormGenerator\Domain\Contract\ThemeInterface;

final class DefaultTheme implements ThemeInterface
{
    public function formClass(): string { return 'pfg-form'; }
    public function rowClass(): string { return 'pfg-row'; }
    public function labelClass(): string { return 'pfg-label'; }
    public function inputClass(): string { return 'pfg-input'; }
    public function errorClass(): string { return 'pfg-error'; }
    public function helpClass(): string { return 'pfg-help'; }
    public function fieldsetClass(): string { return 'pfg-fieldset'; }
}

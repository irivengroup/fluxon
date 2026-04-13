<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html\Theme;

use Iriven\PhpFormGenerator\Domain\Contract\ThemeInterface;

final class Bootstrap5Theme implements ThemeInterface
{
    public function formClass(): string { return 'row g-3'; }
    public function rowClass(): string { return 'col-12'; }
    public function labelClass(): string { return 'form-label'; }
    public function inputClass(): string { return 'form-control'; }
    public function errorClass(): string { return 'invalid-feedback d-block'; }
    public function helpClass(): string { return 'form-text'; }
    public function fieldsetClass(): string { return 'border rounded p-3 mb-3'; }
}

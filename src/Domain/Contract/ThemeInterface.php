<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface ThemeInterface
{
    public function formClass(): string;
    public function rowClass(): string;
    public function labelClass(): string;
    public function inputClass(): string;
    public function errorClass(): string;
    public function helpClass(): string;
    public function fieldsetClass(): string;
}

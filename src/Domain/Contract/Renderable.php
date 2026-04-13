<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface Renderable
{
    public function render(): string;
}

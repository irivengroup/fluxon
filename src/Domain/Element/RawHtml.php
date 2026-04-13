<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;

final class RawHtml implements Renderable
{
    public function __construct(private readonly string $html) {}
    public function render(): string { return $this->html; }
}

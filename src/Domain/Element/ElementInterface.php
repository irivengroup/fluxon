<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

use Iriven\PhpFormGenerator\Domain\Contract\Renderable;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlAttributes;

interface ElementInterface extends Renderable
{
    public function attributes(): HtmlAttributes;

    public function name(): string;

    public function setValue(mixed $value): void;

    public function shouldForceMultipart(): bool;
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

final class FileElement extends InputElement
{
    public function __construct(string $label, array $attributes = [])
    {
        parent::__construct($label, 'file', $attributes);
    }

    public function shouldForceMultipart(): bool
    {
        return true;
    }
}

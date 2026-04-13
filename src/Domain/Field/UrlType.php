<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class UrlType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'url';
    }
}

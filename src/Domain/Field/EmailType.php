<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class EmailType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'email';
    }
}

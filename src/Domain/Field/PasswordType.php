<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class PasswordType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'password';
    }
}
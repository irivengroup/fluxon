<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class PasswordType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'password';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['attr']['minlength'] ??= '6';
        $options['attr']['maxlength'] ??= '64';
        $options['attr']['placeholder'] ??= '**********';
        return $options;
    }
}

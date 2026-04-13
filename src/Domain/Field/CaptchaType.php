<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class CaptchaType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'captcha';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['mapped'] ??= false;
        return $defaults;
    }
}
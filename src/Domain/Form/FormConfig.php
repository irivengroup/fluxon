<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

final class FormConfig
{
    public function __construct(
        public readonly string $name,
        public readonly string $method = 'POST',
        public readonly string $action = '',
        public readonly bool $csrfProtection = true,
        public readonly string $csrfFieldName = '_token',
        public readonly string $csrfTokenId = 'default_form',
        public readonly bool $allowExtraFields = false,
        public readonly array $attr = [],
        public readonly ?string $dataClass = null,
    ) {
    }
}

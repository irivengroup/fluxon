<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

final class UiComponentResolver
{
    public function resolve(string $fieldType): string
    {
        $normalized = strtolower($fieldType);

        return match (true) {
            str_contains($normalized, 'emailtype') => 'input:email',
            str_contains($normalized, 'passwordtype') => 'input:password',
            str_contains($normalized, 'hiddentype') => 'input:hidden',
            str_contains($normalized, 'textareatype') => 'textarea',
            str_contains($normalized, 'selecttype') => 'select',
            str_contains($normalized, 'radiotype') => 'choice:radio',
            str_contains($normalized, 'checkboxtype') => 'choice:checkbox',
            str_contains($normalized, 'datalisttype') => 'input:datalist',
            str_contains($normalized, 'filetype') => 'input:file',
            str_contains($normalized, 'collectiontype') => 'collection',
            default => 'input:text',
        };
    }
}

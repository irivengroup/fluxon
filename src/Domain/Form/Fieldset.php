<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

final class Fieldset
{
    /**
     * @param array<string, mixed> $options
     * @param array<int, string> $fields
     * @param array<int, Fieldset> $children
     */
    public function __construct(
        public string $id,
        public array $options,
        public array $fields = [],
        public array $children = [],
    ) {
    }
}

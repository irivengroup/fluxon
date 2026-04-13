<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

final class FormView
{
    /**
     * @param array<string, scalar|array|null> $vars
     * @param array<string, FormView> $children
     * @param list<string> $errors
     */
    public function __construct(
        public array $vars = [],
        public array $children = [],
        public array $errors = [],
    ) {
    }
}

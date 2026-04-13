<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\ValueObject;

final class ChoiceOption
{
    public function __construct(
        public readonly string $label,
        public readonly string|int|bool $value
    ) {
    }
}

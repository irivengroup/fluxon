<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class FormField
{
    public function __construct(
        public string $type = 'TextType',
        public bool $required = false,
        public ?string $label = null,
    ) {
    }
}

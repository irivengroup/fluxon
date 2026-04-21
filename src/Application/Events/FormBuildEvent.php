<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Events;

use Iriven\PhpFormGenerator\Domain\Form\Form;

/** @api */
final class FormBuildEvent
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public Form $form,
        public array $context = [],
    ) {
    }
}

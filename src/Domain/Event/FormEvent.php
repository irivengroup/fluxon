<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Event;

use Iriven\PhpFormGenerator\Domain\Form\Form;

class FormEvent
{
    /** @param array<string, mixed> $context */
    public function __construct(
        private readonly Form $form,
        private mixed $data = null,
        private array $context = [],
    ) {
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /** @return array<string, mixed> */
    public function getContext(): array
    {
        return $this->context;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Event;

use Iriven\PhpFormGenerator\Domain\Form\FormInterface;

class FormEvent
{
    public function __construct(
        private readonly FormInterface $form,
        private mixed $data = null
    ) {
    }

    public function getForm(): FormInterface
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
}

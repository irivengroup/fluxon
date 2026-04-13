<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Form\FormInterface;

interface FormBuilderInterface
{
    public function add(string $name, string $type, array $options = []): self;
    public function remove(string $name): self;
    public function setData(mixed $data): self;
    public function addEventListener(string $eventName, callable $listener): self;
    public function addFormConstraint(ConstraintInterface $constraint): self;
    public function getForm(): FormInterface;
}

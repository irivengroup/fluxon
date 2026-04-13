<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;

final class FormBuilder
{
    private Form $form;
    /** @var list<Fieldset> */
    private array $fieldsetStack = [];

    public function __construct(
        string $name,
        array $options = [],
        ?CsrfManagerInterface $csrfManager = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?DataMapperInterface $dataMapper = null,
    ) {
        $this->form = new Form($name, $options, $csrfManager, $dispatcher, $dataMapper);
    }

    public function add(string $name, string $type, array $options = []): self
    {
        /** @var FieldTypeInterface $instance */
        $instance = new $type();
        $normalized = $instance->normalizeOptions($options);

        $field = new FieldDefinition(
            $name,
            $instance,
            $normalized,
            $normalized['constraints'] ?? [],
            $normalized['data'] ?? null,
        );

        $current = $this->currentFieldset();
        if ($current !== null) {
            $current->addField($field);
        }

        $this->form->addField($field);

        return $this;
    }

    public function addFieldset(array $options = []): self
    {
        $fieldset = new Fieldset($options);
        $current = $this->currentFieldset();
        if ($current !== null) {
            $current->addChild($fieldset);
        } else {
            $this->form->addFieldset($fieldset);
        }
        $this->fieldsetStack[] = $fieldset;

        return $this;
    }

    public function endFieldset(): self
    {
        array_pop($this->fieldsetStack);
        return $this;
    }

    public function setData(mixed $data): self
    {
        $this->form->setData($data);
        return $this;
    }

    public function addFormConstraint(callable $constraint): self
    {
        $this->form->addFormConstraint($constraint);
        return $this;
    }

    public function useType(string $typeClass, array $options = []): self
    {
        /** @var FormTypeInterface $type */
        $type = new $typeClass();
        $type->buildForm($this, $options);
        return $this;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    private function currentFieldset(): ?Fieldset
    {
        if ($this->fieldsetStack === []) {
            return null;
        }
        return $this->fieldsetStack[array_key_last($this->fieldsetStack)];
    }
}

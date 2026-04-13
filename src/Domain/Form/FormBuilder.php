<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormBuilderInterface;
use Iriven\PhpFormGenerator\Domain\Event\EventDispatcher;

final class FormBuilder implements FormBuilderInterface
{
    /**
     * @var array<string, Field>
     */
    private array $fields = [];

    /**
     * @var list<ConstraintInterface>
     */
    private array $formConstraints = [];

    private mixed $data = [];

    public function __construct(
        private readonly string $name,
        private readonly DataMapperInterface $dataMapper,
        private readonly ?CsrfManagerInterface $csrfManager,
        private readonly EventDispatcher $eventDispatcher,
        private readonly array $formOptions = [],
    ) {
    }

    public function add(string $name, string $type, array $options = []): self
    {
        /** @var FieldTypeInterface $instance */
        $instance = new $type();
        $resolved = $instance->configureOptions($options);
        $field = new Field($name, $instance, $resolved);
        $instance->buildField($field, $resolved);
        $this->fields[$name] = $field;

        return $this;
    }

    public function remove(string $name): self
    {
        unset($this->fields[$name]);
        return $this;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function addEventListener(string $eventName, callable $listener): self
    {
        $this->eventDispatcher->addListener($eventName, $listener);
        return $this;
    }

    public function addFormConstraint(ConstraintInterface $constraint): self
    {
        $this->formConstraints[] = $constraint;
        return $this;
    }

    public function getForm(): FormInterface
    {
        $config = new FormConfig(
            name: $this->name,
            method: strtoupper((string) ($this->formOptions['method'] ?? 'POST')),
            action: (string) ($this->formOptions['action'] ?? ''),
            csrfProtection: (bool) ($this->formOptions['csrf_protection'] ?? true),
            csrfFieldName: (string) ($this->formOptions['csrf_field_name'] ?? '_token'),
            csrfTokenId: (string) ($this->formOptions['csrf_token_id'] ?? $this->name),
            allowExtraFields: (bool) ($this->formOptions['allow_extra_fields'] ?? false),
            attr: (array) ($this->formOptions['attr'] ?? []),
            dataClass: isset($this->formOptions['data_class']) ? (string) $this->formOptions['data_class'] : null,
        );

        return new Form(
            config: $config,
            fields: $this->fields,
            dataMapper: $this->dataMapper,
            csrfManager: $this->csrfManager,
            eventDispatcher: $this->eventDispatcher,
            formConstraints: $this->formConstraints,
            data: $this->data,
        );
    }
}

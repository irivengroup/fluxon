<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\FormEvent;
use Iriven\PhpFormGenerator\Domain\Event\FormEvents;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ArrayDataMapper;

final class Form
{
    /** @var list<FieldDefinition> */
    private array $fields = [];
    /** @var list<Fieldset> */
    private array $fieldsets = [];
    /** @var list<callable(array<string,mixed>): list<string>> */
    private array $formConstraints = [];
    private bool $submitted = false;
    private bool $valid = true;
    /** @var list<string> */
    private array $errors = [];
    private mixed $data = null;

    public function __construct(
        private readonly string $name,
        private readonly array $options = [],
        private readonly ?CsrfManagerInterface $csrfManager = null,
        private readonly ?EventDispatcherInterface $dispatcher = null,
        private readonly ?DataMapperInterface $dataMapper = null,
    ) {
    }

    public function addField(FieldDefinition $field): void
    {
        $this->fields[] = $field;
    }

    public function addFieldset(Fieldset $fieldset): void
    {
        $this->fieldsets[] = $fieldset;
    }

    public function addFormConstraint(callable $constraint): void
    {
        $this->formConstraints[] = $constraint;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
        if (is_array($data)) {
            foreach ($this->fields as $field) {
                if (array_key_exists($field->name, $data)) {
                    $field->value = $data[$field->name];
                }
            }
        } elseif (is_object($data)) {
            foreach ($this->fields as $field) {
                if (isset($data->{$field->name}) || property_exists($data, $field->name)) {
                    $field->value = $data->{$field->name};
                }
            }
        }

        $this->dispatcher?->dispatch(FormEvents::PRE_SET_DATA, new FormEvent($this, $data));
    }

    public function handleRequest(RequestInterface $request): void
    {
        $method = strtoupper((string) ($this->options['method'] ?? 'POST'));
        if (strtoupper($request->method()) !== $method) {
            return;
        }

        $this->submitted = true;
        $this->valid = true;
        $this->errors = [];

        $submitted = $request->all();
        $this->dispatcher?->dispatch(FormEvents::PRE_SUBMIT, new FormEvent($this, $submitted));

        if (($this->options['csrf_protection'] ?? false) === true && $this->csrfManager !== null) {
            $tokenField = (string) ($this->options['csrf_field_name'] ?? '_token');
            $tokenId = (string) ($this->options['csrf_token_id'] ?? $this->name);
            $token = $request->input($tokenField);
            if (!$this->csrfManager->isTokenValid($tokenId, is_string($token) ? $token : null)) {
                $this->valid = false;
                $this->errors[] = 'Invalid CSRF token.';
            }
        }

        $data = [];
        foreach ($this->fields as $field) {
            $normalized = $request->input($field->name, $field->value);
            if ($field->type->renderType() === 'checkbox') {
                $normalized = $request->has($field->name) ? ($field->options['checked_value'] ?? '1') : ($field->options['unchecked_value'] ?? '0');
            }
            if ($field->type->renderType() === 'file') {
                $files = $request->files();
                $normalized = $files[$field->name] ?? null;
            }

            $field->value = $normalized;
            $field->errors = [];
            $context = ['field' => $field, 'data' => &$data, 'form' => $this];
            foreach ($field->constraints as $constraint) {
                foreach ($constraint->validate($field->value, $context) as $error) {
                    $field->errors[] = $error;
                    $this->valid = false;
                }
            }

            if ($field->mapped()) {
                $data[$field->name] = $field->value;
            }
        }

        foreach ($this->formConstraints as $constraint) {
            foreach ($constraint($data) as $error) {
                $this->errors[] = (string) $error;
                $this->valid = false;
            }
        }

        $this->dispatcher?->dispatch(FormEvents::SUBMIT, new FormEvent($this, $data));

        $mapper = $this->dataMapper ?? new ArrayDataMapper();
        $this->data = $mapper->map($data, $this->data);

        if (!$this->valid) {
            $this->dispatcher?->dispatch(FormEvents::VALIDATION_ERROR, new FormEvent($this, $data));
        }

        $this->dispatcher?->dispatch(FormEvents::POST_SUBMIT, new FormEvent($this, $this->data));
    }

    /** @return list<FieldDefinition> */
    public function fields(): array
    {
        return $this->fields;
    }

    /** @return list<Fieldset> */
    public function fieldsets(): array
    {
        return $this->fieldsets;
    }

    public function field(string $name): ?FieldDefinition
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }

        return null;
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    public function isValid(): bool
    {
        return $this->submitted && $this->valid;
    }

    /** @return list<string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function csrfToken(): ?string
    {
        if (($this->options['csrf_protection'] ?? false) !== true || $this->csrfManager === null) {
            return null;
        }

        return $this->csrfManager->generateToken((string) ($this->options['csrf_token_id'] ?? $this->name));
    }

    public function data(): mixed
    {
        return $this->data;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return array<string,mixed> */
    public function options(): array
    {
        return $this->options;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\EventDispatcher;
use Iriven\PhpFormGenerator\Domain\Event\FormEvent;
use Iriven\PhpFormGenerator\Domain\Event\FormEvents;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
use Iriven\PhpFormGenerator\Presentation\Html\FormViewFactory;

final class Form implements FormInterface
{
    /**
     * @param array<string, Field> $fields
     * @param list<ConstraintInterface> $formConstraints
     * @param list<ValidationError> $errors
     */
    public function __construct(
        private readonly FormConfig $config,
        private array $fields,
        private readonly DataMapperInterface $dataMapper,
        private readonly ?CsrfManagerInterface $csrfManager = null,
        private readonly ?EventDispatcher $eventDispatcher = null,
        private array $formConstraints = [],
        private mixed $data = [],
        private bool $submitted = false,
        private array $errors = [],
    ) {
        $this->setData($this->data);
    }

    public function handleRequest(RequestInterface $request): void
    {
        $this->submitted = false;
        $submittedData = $request->getFormData($this->config->name);
        if ($submittedData === []) {
            return;
        }

        $this->submitted = true;
        $event = new FormEvent($this, $submittedData);
        $this->eventDispatcher?->dispatch(FormEvents::PRE_SUBMIT, $event);
        $submittedData = is_array($event->getData()) ? $event->getData() : [];

        $this->clearErrors();

        if ($this->config->csrfProtection && $this->csrfManager !== null) {
            $token = isset($submittedData[$this->config->csrfFieldName]) ? (string) $submittedData[$this->config->csrfFieldName] : null;
            if (!$this->csrfManager->isTokenValid($this->config->csrfTokenId, $token)) {
                $this->errors[] = new ValidationError('Invalid CSRF token.');
            }
        }

        foreach ($this->fields as $name => $field) {
            $raw = $submittedData[$name] ?? null;
            $options = $field->getOptions();
            if (($options['type'] ?? null) === 'collection') {
                $entries = is_array($raw) ? $raw : [];
                $entryType = $options['entry_type'];
                $normalized = [];
                foreach ($entries as $entry) {
                    $normalized[] = $entry;
                }
                $field->setData($normalized);
            } else {
                $value = $field->getType()->transformToModel($raw, $options);
                foreach ($options['transformers'] ?? [] as $transformer) {
                    $value = $transformer->reverseTransform($value);
                }
                $field->setData($value);
            }
        }

        $this->data = $this->dataMapper->mapFieldsToData($this->fields, $this->data);

        foreach ($this->fields as $name => $field) {
            $errors = [];
            foreach ($field->getOptions()['constraints'] ?? [] as $constraint) {
                foreach ($constraint->validate($field->getData(), [
                    'field' => $name,
                    'form' => $this,
                    'options' => $field->getOptions(),
                    'data' => $this->data,
                ]) as $error) {
                    $errors[] = $error;
                }
            }
            $field->setErrors($errors);
            if ($errors !== []) {
                $this->eventDispatcher?->dispatch(FormEvents::VALIDATION_ERROR, new FormEvent($this, $errors));
            }
        }

        foreach ($this->formConstraints as $constraint) {
            foreach ($constraint->validate($this->data, ['form' => $this]) as $error) {
                $this->errors[] = $error;
            }
        }

        $this->eventDispatcher?->dispatch(FormEvents::SUBMIT, new FormEvent($this, $this->data));
        $this->eventDispatcher?->dispatch(FormEvents::POST_SUBMIT, new FormEvent($this, $this->data));
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    public function isValid(): bool
    {
        if (!$this->submitted) {
            return false;
        }

        if ($this->errors !== []) {
            return false;
        }

        foreach ($this->fields as $field) {
            if ($field->getErrors() !== []) {
                return false;
            }
        }

        return true;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
        $this->eventDispatcher?->dispatch(FormEvents::PRE_SET_DATA, new FormEvent($this, $data));
        $this->dataMapper->mapDataToFields($data, $this->fields);
    }

    public function createView(): FormView
    {
        return (new FormViewFactory())->create($this);
    }

    public function getErrors(bool $deep = true): array
    {
        $errors = $this->errors;

        if ($deep) {
            foreach ($this->fields as $field) {
                $errors = [...$errors, ...$field->getErrors()];
            }
        }

        return $errors;
    }

    public function get(string $name): Field
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown field "%s".', $name));
        }

        return $this->fields[$name];
    }

    /**
     * @return array<string, Field>
     */
    public function all(): array
    {
        return $this->fields;
    }

    public function getName(): string
    {
        return $this->config->name;
    }

    public function getConfig(): FormConfig
    {
        return $this->config;
    }

    public function getCsrfToken(): ?string
    {
        if (!$this->config->csrfProtection || $this->csrfManager === null) {
            return null;
        }

        return $this->csrfManager->generateToken($this->config->csrfTokenId);
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->getName()] = $field;
    }

    private function clearErrors(): void
    {
        $this->errors = [];
        foreach ($this->fields as $field) {
            $field->clearErrors();
        }
    }
}

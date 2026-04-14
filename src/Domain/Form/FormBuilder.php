<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventSubscriberInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Constraint\MimeType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Infrastructure\Event\EventDispatcher;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;
use Iriven\PhpFormGenerator\Infrastructure\Security\SessionCaptchaManager;

final class FormBuilder
{
    /** @var array<string, FieldConfig> */
    private array $fields = [];

    /** @var list<Fieldset> */
    private array $fieldsets = [];

    /** @var list<string> */
    private array $fieldsetStack = [];

    /** @var list<ConstraintInterface> */
    private array $formConstraints = [];

    private EventDispatcherInterface $eventDispatcher;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly string $name = 'form',
        private mixed $data = null,
        private array $options = [],
    ) {
        $dispatcher = $this->options['event_dispatcher'] ?? null;
        $this->eventDispatcher = $dispatcher instanceof EventDispatcherInterface ? $dispatcher : new EventDispatcher();
        if (!isset($this->options['captcha_manager'])) {
            $this->options['captcha_manager'] = new SessionCaptchaManager();
        }
    }

    /** @param array<string, mixed> $options */
    public function mergeOptions(array $options): self
    {
        if (!array_key_exists('csrf_protection', $options) && !array_key_exists('csrf_protection', $this->options)) {
            $options['csrf_protection'] = true;
        }
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * @param class-string $typeClass
     * @param array<string, mixed> $options
     */
    public function add(string $name, string $typeClass, array $options = []): self
    {
        /** @var list<ConstraintInterface> $constraints */
        $constraints = is_array($options['constraints'] ?? null) ? $options['constraints'] : [];
        /** @var list<DataTransformerInterface> $transformers */
        $transformers = is_array($options['transformers'] ?? null) ? $options['transformers'] : [];

        if (method_exists($typeClass, 'defaultTransformers')) {
            /** @var list<DataTransformerInterface> $defaults */
            $defaults = $typeClass::defaultTransformers();
            $transformers = array_merge($defaults, $transformers);
        }

        unset($options['constraints'], $options['transformers']);

        $compound = false;
        $collection = false;
        $children = [];
        $entryType = null;
        $entryOptions = [];

        if (is_subclass_of($typeClass, FormTypeInterface::class)) {
            $subBuilder = new self($name, null, $options + ['event_dispatcher' => $this->eventDispatcher]);
            $type = new $typeClass();
            $resolver = new OptionsResolver();
            $type->configureOptions($resolver);
            $resolved = $resolver->resolve($options);
            $type->buildForm($subBuilder, $resolved);
            $compound = true;
            $children = $subBuilder->all();
            $options = $resolved;
        }

        if ($typeClass === CollectionType::class) {
            $collection = true;
            $compound = true;
            $entryType = is_string($options['entry_type'] ?? null) ? $options['entry_type'] : null;
            /** @var array<string, mixed> $entryOptions */
            $entryOptions = is_array($options['entry_options'] ?? null) ? $options['entry_options'] : [];
        }

        if (is_a($typeClass, CaptchaType::class, true)) {
            $options['min_length'] = max(5, (int) ($options['min_length'] ?? 5));
            $options['max_length'] = min(8, max((int) ($options['min_length'] ?? 5), (int) ($options['max_length'] ?? 8)));
            $options['case_sensitive'] = true;
            $options['pattern'] = '[A-Za-z0-9]{' . $options['min_length'] . ',' . $options['max_length'] . '}';
            $options['autocomplete'] = 'off';
            $options['spellcheck'] = 'false';
        }

        if (is_a($typeClass, FileType::class, true)) {
            /** @var array<string, mixed> $attr */
            $attr = is_array($this->options['attr'] ?? null) ? $this->options['attr'] : [];
            $attr['enctype'] = 'multipart/form-data';
            $this->options['attr'] = $attr;

            if (!isset($this->options['method'])) {
                $this->options['method'] = 'POST';
            }

            /** @var array<string, mixed> $fieldAttr */
            $fieldAttr = is_array($options['attr'] ?? null) ? $options['attr'] : [];
            if (method_exists($typeClass, 'acceptAttribute') && !isset($fieldAttr['accept'])) {
                $accept = $typeClass::acceptAttribute();
                if (is_string($accept) && $accept !== '') {
                    $fieldAttr['accept'] = $accept;
                }
            }
            $options['attr'] = $fieldAttr;

            if (method_exists($typeClass, 'allowedMimeTypes')) {
                /** @var list<string> $allowedMimeTypes */
                $allowedMimeTypes = $typeClass::allowedMimeTypes();
                if ($allowedMimeTypes !== []) {
                    $hasMimeConstraint = false;
                    foreach ($constraints as $constraint) {
                        if ($constraint instanceof MimeType) {
                            $hasMimeConstraint = true;
                            break;
                        }
                    }

                    if (!$hasMimeConstraint) {
                        $constraints[] = new MimeType(
                            $allowedMimeTypes,
                            sprintf('The uploaded file must be a valid %s file.', strtolower((new \ReflectionClass($typeClass))->getShortName())),
                        );
                    }
                }
            }
        }

        $this->fields[$name] = new FieldConfig(
            $name,
            $typeClass,
            $options,
            $constraints,
            $transformers,
            $children,
            $compound,
            $collection,
            $entryType,
            $entryOptions,
            [],
        );

        if ($this->fieldsetStack !== []) {
            $currentId = $this->fieldsetStack[array_key_last($this->fieldsetStack)];
            foreach ($this->fieldsets as $fieldset) {
                if ($fieldset->id === $currentId) {
                    $fieldset->fields[] = $name;
                    break;
                }
            }
        }

        return $this;
    }

    /** @param array<string, mixed> $options */
    public function addFieldset(array $options = []): self
    {
        $fieldset = new Fieldset('fs_' . (count($this->fieldsets) + 1), $options, [], []);

        if ($this->fieldsetStack !== []) {
            $parentId = $this->fieldsetStack[array_key_last($this->fieldsetStack)];
            foreach ($this->fieldsets as $existing) {
                if ($existing->id === $parentId) {
                    $existing->children[] = $fieldset;
                    break;
                }
            }
        } else {
            $this->fieldsets[] = $fieldset;
        }

        $this->fieldsetStack[] = $fieldset->id;

        return $this;
    }

    public function endFieldset(): self
    {
        array_pop($this->fieldsetStack);
        return $this;
    }

    public function addFormConstraint(ConstraintInterface $constraint): self
    {
        $this->formConstraints[] = $constraint;
        return $this;
    }

    public function addEventListener(string $eventName, callable $listener): self
    {
        $this->eventDispatcher->addListener($eventName, $listener);
        return $this;
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber): self
    {
        $this->eventDispatcher->addSubscriber($subscriber);
        return $this;
    }

    /** @return array<string, FieldConfig> */
    public function all(): array
    {
        return $this->fields;
    }

    public function getForm(): Form
    {
        $options = $this->options + [
            'method' => 'POST',
            'action' => '',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => $this->name,
            'csrf_manager' => new NullCsrfManager(),
            'event_dispatcher' => $this->eventDispatcher,
        ];

        return new Form(
            $this->name,
            $this->fields,
            $this->eventDispatcher,
            $options,
            $this->data,
            $this->fieldsets,
            $this->formConstraints,
        );
    }
}

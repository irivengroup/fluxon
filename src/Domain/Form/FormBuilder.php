<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Constraint\MimeType;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventSubscriberInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Infrastructure\Event\EventDispatcher;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;
use Iriven\PhpFormGenerator\Infrastructure\Security\SessionCaptchaManager;
use Iriven\PhpFormGenerator\Infrastructure\Type\TypeResolver;
use ReflectionClass;

final class FormBuilder
{
    /** @var array<string, FieldConfig> */
    private array $fields = [];

    /** @var array<int, Fieldset> */
    private array $fieldsets = [];

    /** @var array<int, string> */
    private array $fieldsetStack = [];

    /** @var array<int, ConstraintInterface> */
    private array $formConstraints = [];

    private EventDispatcherInterface $eventDispatcher;
    private ExtensionRegistry $extensionRegistry;

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

        $registry = $this->options['extension_registry'] ?? null;
        $this->extensionRegistry = $registry instanceof ExtensionRegistry ? $registry : new ExtensionRegistry();
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
     * @param string $typeClass
     * @param array<string, mixed> $options
     */
    public function add(string $name, string $typeClass, array $options = []): self
    {
        $resolvedTypeClass = $this->resolveTypeClass($typeClass);
        $constraints = $this->extractConstraints($options);
        $transformers = $this->extractTransformers($options);
        $options['validation_groups'] = $options['validation_groups'] ?? ['Default'];

        $transformers = $this->mergeDefaultTransformers($resolvedTypeClass, $transformers);

        [$options, $constraints, $transformers] = $this->applyFieldExtensions(
            $resolvedTypeClass,
            $options,
            $constraints,
            $transformers
        );

        unset($options['constraints'], $options['transformers']);

        [$options, $constraints, $children, $compound, $collection, $entryType, $entryOptions] =
            $this->normalizeFieldDefinition($name, $resolvedTypeClass, $options, $constraints);

        $this->fields[$name] = new FieldConfig(
            $name,
            $resolvedTypeClass,
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

        $this->attachFieldToCurrentFieldset($name);

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

        foreach ($this->extensionRegistry->formExtensions() as $extension) {
            $options = $extension->extendFormOptions($options);
        }

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

    private function resolveTypeClass(string $typeClass): string
    {
        $resolvedTypeClass = TypeResolver::resolveFieldType($typeClass);

        if ($resolvedTypeClass === '') {
            throw new InvalidArgumentException('Resolved field type must be a non-empty string.');
        }

        return $resolvedTypeClass;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<int, ConstraintInterface>
     */
    private function extractConstraints(array $options): array
    {
        $constraints = $options['constraints'] ?? [];

        return is_array($constraints) ? array_values($constraints) : [];
    }

    /**
     * @param array<string, mixed> $options
     * @return array<int, DataTransformerInterface>
     */
    private function extractTransformers(array $options): array
    {
        $transformers = $options['transformers'] ?? [];

        return is_array($transformers) ? array_values($transformers) : [];
    }

    /**
     * @param array<int, DataTransformerInterface> $transformers
     * @return array<int, DataTransformerInterface>
     */
    private function mergeDefaultTransformers(string $typeClass, array $transformers): array
    {
        if (!method_exists($typeClass, 'defaultTransformers')) {
            return $transformers;
        }

        /** @var array<int, DataTransformerInterface> $defaults */
        $defaults = $typeClass::defaultTransformers();

        return array_merge($defaults, $transformers);
    }

    /**
     * @param array<string, mixed> $options
     * @param array<int, ConstraintInterface> $constraints
     * @param array<int, DataTransformerInterface> $transformers
     * @return array{0: array<string, mixed>, 1: array<int, ConstraintInterface>, 2: array<int, DataTransformerInterface>}
     */
    private function applyFieldExtensions(
        string $typeClass,
        array $options,
        array $constraints,
        array $transformers
    ): array {
        foreach ($this->extensionRegistry->fieldExtensionsFor($typeClass) as $extension) {
            $options = $extension->extendOptions($options);
            $constraints = $extension->extendConstraints($constraints, $options);
            $transformers = $extension->extendTransformers($transformers, $options);
        }

        return [$options, $constraints, $transformers];
    }

    /**
     * @param array<string, mixed> $options
     * @param array<int, ConstraintInterface> $constraints
     * @return array{
     *     0: array<string, mixed>,
     *     1: array<int, ConstraintInterface>,
     *     2: array<string, FieldConfig>,
     *     3: bool,
     *     4: bool,
     *     5: ?string,
     *     6: array<string, mixed>
     * }
     */
    private function normalizeFieldDefinition(
        string $name,
        string $typeClass,
        array $options,
        array $constraints
    ): array {
        $children = [];
        $compound = false;
        $collection = false;
        $entryType = null;
        $entryOptions = [];

        if (is_subclass_of($typeClass, FormTypeInterface::class)) {
            [$options, $children] = $this->buildCompoundChildren($name, $typeClass, $options);
            $compound = true;
        }

        if ($typeClass === CollectionType::class) {
            $collection = true;
            $compound = true;
            $entryType = is_string($options['entry_type'] ?? null)
                ? TypeResolver::resolveFormType($options['entry_type'])
                : null;
            $entryOptions = is_array($options['entry_options'] ?? null)
                ? $options['entry_options']
                : [];
        }

        $options = $this->normalizeCaptchaOptions($typeClass, $options);
        [$options, $constraints] = $this->normalizeFileFieldOptions($typeClass, $options, $constraints);

        return [$options, $constraints, $children, $compound, $collection, $entryType, $entryOptions];
    }

    /**
     * @param array<string, mixed> $options
     * @return array{0: array<string, mixed>, 1: array<string, FieldConfig>}
     */
    private function buildCompoundChildren(string $name, string $typeClass, array $options): array
    {
        $subBuilder = new self($name, null, $options + [
            'event_dispatcher' => $this->eventDispatcher,
            'extension_registry' => $this->extensionRegistry,
        ]);

        /** @var FormTypeInterface $type */
        $type = new $typeClass();
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $resolved = $resolver->resolve($options);
        $type->buildForm($subBuilder, $resolved);

        return [$resolved, $subBuilder->all()];
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function normalizeCaptchaOptions(string $typeClass, array $options): array
    {
        if (!is_a($typeClass, CaptchaType::class, true)) {
            return $options;
        }

        $options['min_length'] = max(5, (int) ($options['min_length'] ?? 5));
        $options['max_length'] = min(8, max((int) $options['min_length'], (int) ($options['max_length'] ?? 8)));
        $options['case_sensitive'] = true;
        $options['pattern'] = '[A-Za-z0-9]{' . $options['min_length'] . ',' . $options['max_length'] . '}';
        $options['autocomplete'] = 'off';
        $options['spellcheck'] = 'false';

        return $options;
    }

    /**
     * @param array<string, mixed> $options
     * @param array<int, ConstraintInterface> $constraints
     * @return array{0: array<string, mixed>, 1: array<int, ConstraintInterface>}
     */
    private function normalizeFileFieldOptions(string $typeClass, array $options, array $constraints): array
    {
        if (!is_a($typeClass, FileType::class, true)) {
            return [$options, $constraints];
        }

        $formAttr = is_array($this->options['attr'] ?? null) ? $this->options['attr'] : [];
        $formAttr['enctype'] = 'multipart/form-data';
        $this->options['attr'] = $formAttr;

        if (!isset($this->options['method'])) {
            $this->options['method'] = 'POST';
        }

        $fieldAttr = is_array($options['attr'] ?? null) ? $options['attr'] : [];
        if (method_exists($typeClass, 'acceptAttribute') && !isset($fieldAttr['accept'])) {
            $accept = $typeClass::acceptAttribute();
            if (is_string($accept) && $accept !== '') {
                $fieldAttr['accept'] = $accept;
            }
        }
        $options['attr'] = $fieldAttr;

        if (!method_exists($typeClass, 'allowedMimeTypes')) {
            return [$options, $constraints];
        }

        /** @var array<int, string> $allowedMimeTypes */
        $allowedMimeTypes = $typeClass::allowedMimeTypes();
        if ($allowedMimeTypes === [] || $this->hasMimeConstraint($constraints)) {
            return [$options, $constraints];
        }

        $constraints[] = new MimeType(
            $allowedMimeTypes,
            sprintf(
                'The uploaded file must be a valid %s file.',
                strtolower((new ReflectionClass($typeClass))->getShortName())
            ),
        );

        return [$options, $constraints];
    }

    /**
     * @param array<int, ConstraintInterface> $constraints
     */
    private function hasMimeConstraint(array $constraints): bool
    {
        foreach ($constraints as $constraint) {
            if ($constraint instanceof MimeType) {
                return true;
            }
        }

        return false;
    }

    private function attachFieldToCurrentFieldset(string $name): void
    {
        if ($this->fieldsetStack === []) {
            return;
        }

        $currentId = $this->fieldsetStack[array_key_last($this->fieldsetStack)];
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->id === $currentId) {
                $fieldset->fields[] = $name;
                return;
            }
        }
    }
}

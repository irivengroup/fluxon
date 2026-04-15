<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use InvalidArgumentException;
use Iriven\PhpFormGenerator\Domain\Constraint\MimeType;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataTransformerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\Type\TypeResolver;
use ReflectionClass;

final class FormBuilderFieldDefinitionFactory
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ExtensionRegistry $extensionRegistry,
    ) {
    }

    /**
     * @param array<string, mixed> $builderOptions
     * @param array<string, mixed> $options
     * @return array{0: FieldConfig, 1: array<string, mixed>}
     */
    public function create(string $name, string $typeClass, array $options, array $builderOptions): array
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

        [$options, $constraints, $children, $compound, $collection, $entryType, $entryOptions, $builderOptions] =
            $this->normalizeFieldDefinition($name, $resolvedTypeClass, $options, $constraints, $builderOptions);

        return [
            new FieldConfig(
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
            ),
            $builderOptions,
        ];
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
     * @param array<string, mixed> $builderOptions
     * @return array{0: array<string, mixed>, 1: array<int, ConstraintInterface>, 2: array<string, FieldConfig>, 3: bool, 4: bool, 5: ?string, 6: array<string, mixed>, 7: array<string, mixed>}
     */
    private function normalizeFieldDefinition(
        string $name,
        string $typeClass,
        array $options,
        array $constraints,
        array $builderOptions
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
        [$options, $constraints, $builderOptions] = $this->normalizeFileFieldOptions($typeClass, $options, $constraints, $builderOptions);

        return [$options, $constraints, $children, $compound, $collection, $entryType, $entryOptions, $builderOptions];
    }

    /**
     * @param array<string, mixed> $options
     * @return array{0: array<string, mixed>, 1: array<string, FieldConfig>}
     */
    private function buildCompoundChildren(string $name, string $typeClass, array $options): array
    {
        $subBuilder = new FormBuilder($name, null, $options + [
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
     * @param array<string, mixed> $builderOptions
     * @return array{0: array<string, mixed>, 1: array<int, ConstraintInterface>, 2: array<string, mixed>}
     */
    private function normalizeFileFieldOptions(
        string $typeClass,
        array $options,
        array $constraints,
        array $builderOptions
    ): array {
        if (!is_a($typeClass, FileType::class, true)) {
            return [$options, $constraints, $builderOptions];
        }

        $builderOptions = $this->applyMultipartFormOptions($builderOptions);
        $options = $this->applyFileFieldAttributes($typeClass, $options);
        $constraints = $this->applyFileMimeConstraint($typeClass, $constraints);

        return [$options, $constraints, $builderOptions];
    }

    /**
     * @param array<string, mixed> $builderOptions
     * @return array<string, mixed>
     */
    private function applyMultipartFormOptions(array $builderOptions): array
    {
        $formAttr = is_array($builderOptions['attr'] ?? null) ? $builderOptions['attr'] : [];
        $formAttr['enctype'] = 'multipart/form-data';
        $builderOptions['attr'] = $formAttr;
        $builderOptions['method'] = $builderOptions['method'] ?? 'POST';

        return $builderOptions;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function applyFileFieldAttributes(string $typeClass, array $options): array
    {
        $fieldAttr = is_array($options['attr'] ?? null) ? $options['attr'] : [];
        if (method_exists($typeClass, 'acceptAttribute') && !isset($fieldAttr['accept'])) {
            $accept = $typeClass::acceptAttribute();
            if (is_string($accept) && $accept !== '') {
                $fieldAttr['accept'] = $accept;
            }
        }
        $options['attr'] = $fieldAttr;

        return $options;
    }

    /**
     * @param array<int, ConstraintInterface> $constraints
     * @return array<int, ConstraintInterface>
     */
    private function applyFileMimeConstraint(string $typeClass, array $constraints): array
    {
        if (!method_exists($typeClass, 'allowedMimeTypes')) {
            return $constraints;
        }

        /** @var array<int, string> $allowedMimeTypes */
        $allowedMimeTypes = $typeClass::allowedMimeTypes();
        if ($allowedMimeTypes === [] || $this->hasMimeConstraint($constraints)) {
            return $constraints;
        }

        $constraints[] = new MimeType(
            $allowedMimeTypes,
            sprintf(
                'The uploaded file must be a valid %s file.',
                strtolower((new ReflectionClass($typeClass))->getShortName())
            ),
        );

        return $constraints;
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
}

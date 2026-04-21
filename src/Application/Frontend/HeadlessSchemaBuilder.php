<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

use Iriven\PhpFormGenerator\Domain\Form\FieldConfig;
use Iriven\PhpFormGenerator\Domain\Form\Form;

/** @api */
final class HeadlessSchemaBuilder
{
    public function __construct(
        private readonly UiComponentResolver $resolver = new UiComponentResolver(),
        private readonly ValidationExporter $validationExporter = new ValidationExporter(),
        private readonly ?FrontendSchemaRendererConfig $rendererConfig = null,
    ) {
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function build(Form $form, array $schema): array
    {
        $fields = [];
        $validation = [];

        $componentResolver = new AdvancedUiComponentResolver(
            $this->resolver,
            $this->rendererConfig?->componentMap() ?? new UiComponentMap()
        );

        foreach ($form->fields() as $field) {
            $fields[] = $this->fieldSchema($field, $componentResolver);
            $validation[$field->name] = $this->validationExporter->export($field);
        }

        return [
            'form' => [
                'name' => $schema['name'] ?? $form->getName(),
                'method' => $schema['method'] ?? (string) ($form->options()['method'] ?? 'POST'),
                'action' => $schema['action'] ?? ($form->options()['action'] ?? null),
            ],
            'fields' => $fields,
            'ui' => [
                'theme' => $schema['ui']['theme'] ?? 'default',
                'component_overrides' => ($this->rendererConfig?->componentMap()->all()) ?? [],
            ],
            'runtime' => $schema['runtime'] ?? [],
            'validation' => $validation,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fieldSchema(FieldConfig $field, AdvancedUiComponentResolver $componentResolver): array
    {
        return [
            'name' => $field->name,
            'type' => $field->typeClass,
            'component' => $componentResolver->resolve($field->typeClass),
            'props' => $this->props($field),
            'label' => $field->options['label'] ?? null,
            'required' => (bool) ($field->options['required'] ?? false),
            'choices' => $field->options['choices'] ?? [],
            'layout' => [
                'group' => $field->options['group'] ?? null,
                'order' => $field->options['order'] ?? null,
            ],
            'ui_hints' => [
                'placeholder' => $field->options['placeholder'] ?? null,
                'help' => $field->options['help'] ?? null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function props(FieldConfig $field): array
    {
        $defaults = $this->rendererConfig?->defaultProps() ?? [];
        $uiProps = $field->options['ui_props'] ?? [];
        $uiProps = is_array($uiProps) ? $uiProps : [];

        return array_merge($defaults, $uiProps);
    }
}

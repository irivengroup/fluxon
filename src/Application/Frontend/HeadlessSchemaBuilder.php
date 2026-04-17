<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

use Iriven\PhpFormGenerator\Domain\Form\FieldConfig;
use Iriven\PhpFormGenerator\Domain\Form\Form;

final class HeadlessSchemaBuilder
{
    public function __construct(
        private readonly UiComponentResolver $componentResolver = new UiComponentResolver(),
        private readonly ValidationExporter $validationExporter = new ValidationExporter(),
    ) {
    }

    /**
     * @param array<string, mixed> $baseSchema
     * @return array<string, mixed>
     */
    public function build(Form $form, array $baseSchema): array
    {
        return [
            'form' => [
                'name' => $form->getName(),
                'method' => $baseSchema['method'] ?? 'POST',
                'action' => $baseSchema['action'] ?? null,
            ],
            'fields' => $this->fields($form),
            'ui' => $baseSchema['ui'] ?? [],
            'runtime' => $baseSchema['runtime'] ?? [],
            'validation' => $this->validation($form),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(Form $form): array
    {
        $fields = [];

        foreach ($form->fields() as $name => $field) {
            $fields[] = $this->field($name, $field);
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function field(string $name, FieldConfig $field): array
    {
        return [
            'name' => $name,
            'type' => $field->typeClass,
            'component' => $this->componentResolver->resolve($field->typeClass),
            'label' => $field->options['label'] ?? null,
            'required' => (bool) ($field->options['required'] ?? false),
            'choices' => is_array($field->options['choices'] ?? null) ? $field->options['choices'] : [],
            'layout' => [
                'group' => $field->options['group'] ?? null,
                'order' => $field->options['order'] ?? null,
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function validation(Form $form): array
    {
        $validation = [];

        foreach ($form->fields() as $name => $field) {
            $validation[$name] = $this->validationExporter->export($field);
        }

        return $validation;
    }
}

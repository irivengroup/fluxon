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
        foreach ($form->getFields() as $field) {
            $fields[$field->name] = [
                'type' => $field->type,
                'component' => $this->resolver->resolve($field->type),
                'props' => $this->props($field),
                'validation' => $this->validationExporter->export($field),
            ];
        }

        $schema['fields'] = $fields;
        $schema['ui'] = [
            'theme' => $schema['ui']['theme'] ?? 'default',
        ];

        return $schema;
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

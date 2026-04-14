<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormView;

final class JsonSchemaExporter
{
    /** @return array<string, mixed> */
    public function export(Form $form): array
    {
        return $this->exportView($form->createView());
    }

    /** @return array<string, mixed> */
    private function exportView(FormView $view): array
    {
        $schema = [
            'name' => $view->name,
            'fullName' => $view->fullName,
            'id' => $view->id,
            'type' => $view->type,
            'label' => $view->vars['label'] ?? $view->name,
            'required' => (bool) ($view->vars['required'] ?? false),
            'errors' => $view->errors,
            'attr' => $view->vars['attr'] ?? [],
        ];

        if ($view->type === 'collection') {
            $schema['prototype'] = isset($view->vars['prototype_view']) ? $this->exportView($view->vars['prototype_view']) : null;
        }

        if ($view->children !== []) {
            $schema['children'] = array_map(fn (FormView $child): array => $this->exportView($child), $view->children);
        }

        return $schema;
    }
}

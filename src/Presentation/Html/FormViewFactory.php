<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormInterface;
use Iriven\PhpFormGenerator\Domain\Form\FormView;

final class FormViewFactory
{
    public function create(FormInterface $form): FormView
    {
        if (!$form instanceof Form) {
            throw new \InvalidArgumentException('Unsupported form implementation.');
        }

        $config = $form->getConfig();
        $children = [];

        foreach ($form->all() as $field) {
            $options = $field->getOptions();
            $type = $options['type'] ?? $field->getType()->getBlockPrefix();
            $children[$field->getName()] = new FormView(
                vars: [
                    'name' => $field->getName(),
                    'full_name' => $config->name . '[' . $field->getName() . ']',
                    'id' => $config->name . '_' . $field->getName(),
                    'type' => $type,
                    'label' => $options['label'] ?? ucfirst(str_replace('_', ' ', $field->getName())),
                    'value' => $field->getData(),
                    'attr' => $options['attr'] ?? [],
                    'choices' => $options['choices'] ?? [],
                    'required' => (bool) ($options['required'] ?? false),
                    'help' => $options['help'] ?? null,
                    'expanded' => (bool) ($options['expanded'] ?? false),
                    'multiple' => (bool) ($options['multiple'] ?? false),
                    'safe_html' => (bool) ($options['safe_html'] ?? false),
                ],
                children: [],
                errors: array_map(static fn($e) => $e->message, $field->getErrors()),
            );
        }

        if ($config->csrfProtection) {
            $children[$config->csrfFieldName] = new FormView(vars: [
                'name' => $config->csrfFieldName,
                'full_name' => $config->name . '[' . $config->csrfFieldName . ']',
                'id' => $config->name . '_' . $config->csrfFieldName,
                'type' => 'hidden',
                'label' => null,
                'value' => $form->getCsrfToken(),
                'attr' => [],
                'choices' => [],
                'required' => false,
                'help' => null,
                'expanded' => false,
                'multiple' => false,
                'safe_html' => false,
            ]);
        }

        return new FormView(
            vars: [
                'name' => $config->name,
                'method' => $config->method,
                'action' => $config->action,
                'attr' => $config->attr,
            ],
            children: $children,
            errors: array_map(static fn($e) => $e->message, $form->getErrors(false)),
        );
    }
}

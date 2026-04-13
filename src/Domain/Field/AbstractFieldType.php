<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeInterface;
use Iriven\PhpFormGenerator\Domain\Form\Field;

abstract class AbstractFieldType implements FieldTypeInterface
{
    public function configureOptions(array $options = []): array
    {
        return array_replace([
            'label' => null,
            'required' => false,
            'mapped' => true,
            'default' => null,
            'empty_data' => null,
            'attr' => [],
            'row_attr' => [],
            'label_attr' => [],
            'help' => null,
            'help_attr' => [],
            'disabled' => false,
            'readonly' => false,
            'constraints' => [],
            'transformers' => [],
            'choices' => [],
            'expanded' => false,
            'multiple' => false,
            'safe_html' => false,
            'type' => $this->getBlockPrefix(),
        ], $options);
    }

    public function buildField(Field $field, array $options = []): void
    {
    }

    public function transformFromModel(mixed $value, array $options = []): mixed
    {
        return $value;
    }

    public function transformToModel(mixed $value, array $options = []): mixed
    {
        return $value;
    }
}

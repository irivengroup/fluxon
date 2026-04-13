<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Form\Field;

interface FieldTypeInterface
{
    public function getBlockPrefix(): string;

    public function configureOptions(array $options = []): array;

    public function buildField(Field $field, array $options = []): void;

    public function transformFromModel(mixed $value, array $options = []): mixed;

    public function transformToModel(mixed $value, array $options = []): mixed;
}

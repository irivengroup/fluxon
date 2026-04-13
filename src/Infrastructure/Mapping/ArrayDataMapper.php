<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Form\Field;

final class ArrayDataMapper implements DataMapperInterface
{
    public function mapDataToFields(mixed $data, array $fields): void
    {
        $data = is_array($data) ? $data : [];
        foreach ($fields as $name => $field) {
            $value = $data[$name] ?? ($field->getOptions()['default'] ?? null);
            foreach ($field->getOptions()['transformers'] ?? [] as $transformer) {
                $value = $transformer->transform($value);
            }
            $field->setData($field->getType()->transformFromModel($value, $field->getOptions()));
        }
    }

    public function mapFieldsToData(array $fields, mixed $data): mixed
    {
        $target = is_array($data) ? $data : [];
        foreach ($fields as $name => $field) {
            $target[$name] = $field->getData();
        }

        return $target;
    }
}

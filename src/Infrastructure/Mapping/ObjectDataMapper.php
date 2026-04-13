<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;

final class ObjectDataMapper implements DataMapperInterface
{
    public function mapDataToFields(mixed $data, array $fields): void
    {
        if (!is_object($data)) {
            return;
        }

        foreach ($fields as $name => $field) {
            $value = property_exists($data, $name) ? $data->{$name} : ($field->getOptions()['default'] ?? null);
            foreach ($field->getOptions()['transformers'] ?? [] as $transformer) {
                $value = $transformer->transform($value);
            }
            $field->setData($field->getType()->transformFromModel($value, $field->getOptions()));
        }
    }

    public function mapFieldsToData(array $fields, mixed $data): mixed
    {
        if (!is_object($data)) {
            $data = new \stdClass();
        }

        foreach ($fields as $name => $field) {
            $data->{$name} = $field->getData();
        }

        return $data;
    }
}

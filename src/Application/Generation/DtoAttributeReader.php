<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Generation;

use Iriven\PhpFormGenerator\Domain\Attribute\FormField;
use Iriven\PhpFormGenerator\Domain\Attribute\FormIgnore;
use ReflectionClass;
use ReflectionProperty;

/** @api */
final class DtoAttributeReader
{
    /**
     * @param object $dto
     * @return array<string, array{type?: string, required?: bool, label?: string, ignored?: bool}>
     */
    public function read(object $dto): array
    {
        $reflection = new ReflectionClass($dto);
        $result = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            if ($property->getAttributes(FormIgnore::class) !== []) {
                $result[$name] = ['ignored' => true];
                continue;
            }

            $fieldAttributes = $property->getAttributes(FormField::class);
            if ($fieldAttributes !== []) {
                /** @var FormField $field */
                $field = $fieldAttributes[0]->newInstance();
                $result[$name] = [
                    'type' => $field->type,
                    'required' => $field->required,
                    'label' => $field->label,
                ];
            }
        }

        ksort($result);

        return $result;
    }
}

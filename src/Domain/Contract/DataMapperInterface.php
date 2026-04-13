<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Form\Field;

interface DataMapperInterface
{
    public function mapDataToFields(mixed $data, array $fields): void;

    /**
     * @param array<string, Field> $fields
     */
    public function mapFieldsToData(array $fields, mixed $data): mixed;
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;

final class ArrayDataMapper implements DataMapperInterface
{
    public function map(array $submittedData, mixed $target = null): mixed
    {
        return $submittedData;
    }
}

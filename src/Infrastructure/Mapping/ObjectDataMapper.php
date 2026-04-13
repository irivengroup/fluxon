<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;

final class ObjectDataMapper implements DataMapperInterface
{
    public function __construct(private readonly ?string $className = null)
    {
    }

    public function map(array $submittedData, mixed $target = null): mixed
    {
        $object = $target;
        if ($object === null) {
            $class = $this->className;
            if ($class === null) {
                return (object) $submittedData;
            }
            $object = new $class();
        }

        foreach ($submittedData as $key => $value) {
            $object->{$key} = $value;
        }

        return $object;
    }
}

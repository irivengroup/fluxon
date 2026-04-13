<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface DataMapperInterface
{
    public function map(array $submittedData, mixed $target = null): mixed;
}

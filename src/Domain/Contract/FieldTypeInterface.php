<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface FieldTypeInterface
{
    public function renderType(): string;
    public function isCompound(): bool;
    public function isCollection(): bool;
    /** @return array<string,mixed> */
    public function normalizeOptions(array $options): array;
}

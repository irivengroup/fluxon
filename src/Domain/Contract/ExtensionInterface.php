<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

/**
 * @api
 */
interface ExtensionInterface
{
    public function supports(string $type): bool;

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function apply(array $options): array;
}

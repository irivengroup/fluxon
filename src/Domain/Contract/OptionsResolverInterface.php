<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface OptionsResolverInterface
{
    /** @param array<string, mixed> $defaults */
    public function setDefaults(array $defaults): self;

    /** @param array<int, string> $required */
    public function setRequired(array $required): self;

    /** @param string|array<int, string> $types */
    public function setAllowedTypes(string $option, string|array $types): self;

    /** @param callable|array<int, mixed> $values */
    public function setAllowedValues(string $option, callable|array $values): self;

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function resolve(array $options = []): array;
}

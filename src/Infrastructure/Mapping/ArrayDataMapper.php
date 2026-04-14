<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Mapping;

final class ArrayDataMapper
{
    /** @param array<string, mixed> $target */
    public function map(array $target, array $values): array
    {
        return $this->merge($target, $values);
    }

    /** @param array<string, mixed> $target @param array<string, mixed> $values */
    private function merge(array $target, array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value) && isset($target[$key]) && is_array($target[$key])) {
                $target[$key] = $this->merge($target[$key], $value);
                continue;
            }

            $target[$key] = $value;
        }

        return $target;
    }
}

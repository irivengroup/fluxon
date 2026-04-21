<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Mapping;

/** @api */
final class ObjectFormMapper
{
    /**
     * @param object|array<string, mixed> $source
     * @return array<string, mixed>
     */
    public function extract(object|array $source): array
    {
        if (is_array($source)) {
            return $source;
        }

        $result = [];
        foreach (get_object_vars($source) as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $payload
     * @param object|array<string, mixed> $target
     * @return array<string, mixed>
     */
    public function hydrate(array $payload, object|array $target = []): array
    {
        $base = is_array($target) ? $target : $this->extract($target);

        foreach ($payload as $key => $value) {
            $base[(string) $key] = $value;
        }

        return $base;
    }
}

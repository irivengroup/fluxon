<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Mapping;

/** @api */
final class ObjectFormMapper
{
    /**
     * @param mixed $source
     * @return array<string, mixed>
     */
    public function extract(mixed $source): array
    {
        if (is_object($source)) {
            $result = [];
            foreach (get_object_vars($source) as $key => $value) {
                $result[(string) $key] = $value;
            }

            return $result;
        }

        if (is_array($source)) {
            $result = [];
            foreach ($source as $key => $value) {
                $result[(string) $key] = $value;
            }

            return $result;
        }

        return [];
    }

    /**
     * @param array<string, mixed> $payload
     * @param mixed $target
     * @return array<string, mixed>
     */
    public function hydrate(array $payload, mixed $target = []): array
    {
        $base = $this->extract($target);

        foreach ($payload as $key => $value) {
            $base[(string) $key] = $value;
        }

        return $base;
    }
}

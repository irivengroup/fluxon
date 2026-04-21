<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Application\Mapping;
/** @api */
final class MappingBlueprintGenerator
{
    /**
     * @param array<string, mixed> $sample
     */
    public function generate(array $sample): string
    {
        $lines = ['mapping:'];
        foreach (array_keys($sample) as $key) {
            $lines[] = sprintf('  %s: %s', $key, $key);
        }
        return implode("\n", $lines);
    }
}

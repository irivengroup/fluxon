<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Application\Mapping;
/** @api */
final class PropertyPathNormalizer
{
    public function normalize(string $name): string
    {
        return trim($name);
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Application\Headless;
/** @api */
final class HeadlessErrorNormalizer
{
    /** @param array<string, mixed> $errors @return array<string, mixed> */
    public function normalize(array $errors): array { return $errors; }
}

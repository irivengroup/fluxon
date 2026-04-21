<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Application\Headless;
/** @api */
final class HeadlessPayloadNormalizer
{
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function normalize(array $payload): array { return $payload; }
}

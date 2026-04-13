<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface CsrfManagerInterface
{
    public function generateToken(string $tokenId): string;
    public function isTokenValid(string $tokenId, ?string $token): bool;
}

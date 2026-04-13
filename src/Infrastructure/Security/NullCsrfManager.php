<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Security;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;

final class NullCsrfManager implements CsrfManagerInterface
{
    public function generateToken(string $tokenId): string
    {
        return 'dev-token';
    }

    public function isTokenValid(string $tokenId, ?string $token): bool
    {
        return $token === 'dev-token';
    }
}

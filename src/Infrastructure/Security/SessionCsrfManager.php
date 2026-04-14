<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Security;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;

final class SessionCsrfManager implements CsrfManagerInterface
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    public function generateToken(string $tokenId): string
    {
        $_SESSION['_pfg_csrf'] ??= [];
        $_SESSION['_pfg_csrf'][$tokenId] ??= bin2hex(random_bytes(16));

        return (string) $_SESSION['_pfg_csrf'][$tokenId];
    }

    public function isTokenValid(string $tokenId, ?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        $expected = $_SESSION['_pfg_csrf'][$tokenId] ?? null;

        return is_string($expected) && hash_equals($expected, $token);
    }
}

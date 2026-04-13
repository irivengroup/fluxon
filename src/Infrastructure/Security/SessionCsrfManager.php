<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Security;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;

final class SessionCsrfManager implements CsrfManagerInterface
{
    public function __construct(private readonly string $namespace = '_php_form_generator_csrf')
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $_SESSION[$this->namespace] ??= [];
    }

    public function generateToken(string $tokenId): string
    {
        $token = bin2hex(random_bytes(16));
        $_SESSION[$this->namespace][$tokenId] = $token;
        return $token;
    }

    public function isTokenValid(string $tokenId, ?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        return hash_equals((string) ($_SESSION[$this->namespace][$tokenId] ?? ''), $token);
    }
}

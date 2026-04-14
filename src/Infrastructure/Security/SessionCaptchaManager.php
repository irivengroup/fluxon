<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Security;

use Iriven\PhpFormGenerator\Domain\Contract\CaptchaManagerInterface;
use RuntimeException;

final class SessionCaptchaManager implements CaptchaManagerInterface
{
    public function __construct(
        private readonly int $ttlSeconds = 300,
        private readonly int $maxAttempts = 5,
    ) {
        $this->ensureSessionStarted();
        $_SESSION['_pfg_captcha'] ??= [];
        $_SESSION['_pfg_captcha_meta'] ??= [];
    }

    public function generateCode(string $key, int $minLength = 5, int $maxLength = 8): string
    {
        $minLength = max(5, $minLength);
        $maxLength = max($minLength, min(8, $maxLength));

        $length = random_int($minLength, $maxLength);
        $code = $this->generateCaseSensitiveCode($length);

        // Legacy-compatible plain string storage
        $_SESSION['_pfg_captcha'][$key] = $code;

        // Hardened metadata storage
        $_SESSION['_pfg_captcha_meta'][$key] = [
            'expires_at' => time() + max(30, $this->ttlSeconds),
            'attempts_left' => max(1, $this->maxAttempts),
        ];

        return $code;
    }

    public function isCodeValid(string $key, ?string $input): bool
    {
        if ($input === null || $input === '') {
            return false;
        }

        $expected = $_SESSION['_pfg_captcha'][$key] ?? null;
        $meta = $_SESSION['_pfg_captcha_meta'][$key] ?? null;

        if (!is_string($expected)) {
            unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
            return false;
        }

        if (is_array($meta)) {
            $expiresAt = $meta['expires_at'] ?? null;
            $attemptsLeft = $meta['attempts_left'] ?? null;

            if (!is_int($expiresAt) || !is_int($attemptsLeft)) {
                unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
                return false;
            }

            if ($expiresAt < time()) {
                unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
                return false;
            }

            if ($attemptsLeft <= 0) {
                unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
                return false;
            }
        }

        $isValid = hash_equals($expected, $input);

        if ($isValid) {
            unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
            return true;
        }

        if (is_array($meta)) {
            $meta['attempts_left'] = ((int) ($meta['attempts_left'] ?? 1)) - 1;

            if ($meta['attempts_left'] <= 0) {
                unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
            } else {
                $_SESSION['_pfg_captcha_meta'][$key] = $meta;
            }
        }

        return false;
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (headers_sent($file, $line)) {
            throw new RuntimeException(sprintf(
                'Unable to start session for captcha storage because headers were already sent in %s on line %d.',
                $file,
                $line
            ));
        }

        session_start();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Unable to start session for captcha storage.');
        }
    }

    private function generateCaseSensitiveCode(int $length): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghijkmnopqrstuvwxyz';
        $digits = '23456789';
        $all = $uppercase . $lowercase . $digits;

        $chars = [
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $lowercase[random_int(0, strlen($lowercase) - 1)],
        ];

        while (count($chars) < $length) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }

        shuffle($chars);

        return implode('', $chars);
    }
}

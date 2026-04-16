<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Security;

use Iriven\PhpFormGenerator\Domain\Contract\CaptchaManagerInterface;
use RuntimeException;

final class SessionCaptchaManager implements CaptchaManagerInterface
{
    public function __construct()
    {
        $this->ensureSessionStarted();
        $_SESSION['_pfg_captcha'] ??= [];
        $_SESSION['_pfg_captcha_meta'] ??= [];
    }

    public function generateCode(string $key, int $minLength = 5, int $maxLength = 8): string
    {
        $this->ensureSessionStarted();

        $minLength = max(1, $minLength);
        $maxLength = max($minLength, $maxLength);
        $length = random_int($minLength, $maxLength);

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $code = '';
        $maxIndex = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $maxIndex)];
        }

        $_SESSION['_pfg_captcha'][$key] = $code;
        $_SESSION['_pfg_captcha_meta'][$key] = [
            'expires_at' => time() + 300,
            'attempts_left' => 5,
        ];

        return $code;
    }

    public function getCode(string $key): ?string
    {
        $this->ensureSessionStarted();

        $code = $_SESSION['_pfg_captcha'][$key] ?? null;

        return is_string($code) ? $code : null;
    }

    public function isCodeValid(string $key, ?string $input): bool
    {
        $challenge = $this->loadChallenge($key, $input);

        if ($challenge === null) {
            return false;
        }

        [$expected, $meta, $inputValue] = $challenge;

        if ($this->shouldRejectChallenge($key, $meta)) {
            return false;
        }

        if ($this->matchesExpectedCode($expected, $inputValue)) {
            $this->clearChallenge($key);

            return true;
        }

        $this->decrementAttempts($key, $meta);

        return false;
    }

    public function clear(string $key): void
    {
        $this->ensureSessionStarted();
        $this->clearChallenge($key);
    }

    /**
     * @return array{0:string,1:array{expires_at:int,attempts_left:int},2:string}|null
     */
    private function loadChallenge(string $key, ?string $input): ?array
    {
        if ($input === null || $input === '') {
            return null;
        }

        $this->ensureSessionStarted();

        $expected = $_SESSION['_pfg_captcha'][$key] ?? null;
        $meta = $this->metaForKey($key);

        if (!is_string($expected) || $meta === null) {
            $this->clearChallenge($key);

            return null;
        }

        return [$expected, $meta, $input];
    }

    /**
     * @param array{expires_at:int,attempts_left:int} $meta
     */
    private function shouldRejectChallenge(string $key, array $meta): bool
    {
        if (!$this->isExpired($meta) && !$this->hasNoAttemptsLeft($meta)) {
            return false;
        }

        $this->clearChallenge($key);

        return true;
    }

    /**
     * @return array{expires_at:int,attempts_left:int}|null
     */
    private function metaForKey(string $key): ?array
    {
        $meta = $_SESSION['_pfg_captcha_meta'][$key] ?? null;

        if (!is_array($meta)) {
            return null;
        }

        $expiresAt = $meta['expires_at'] ?? null;
        $attemptsLeft = $meta['attempts_left'] ?? null;

        if (!is_int($expiresAt) || !is_int($attemptsLeft)) {
            return null;
        }

        return [
            'expires_at' => $expiresAt,
            'attempts_left' => $attemptsLeft,
        ];
    }

    /**
     * @param array{expires_at:int,attempts_left:int} $meta
     */
    private function isExpired(array $meta): bool
    {
        return $meta['expires_at'] < time();
    }

    /**
     * @param array{expires_at:int,attempts_left:int} $meta
     */
    private function hasNoAttemptsLeft(array $meta): bool
    {
        return $meta['attempts_left'] <= 0;
    }

    private function matchesExpectedCode(string $expected, string $input): bool
    {
        return hash_equals($expected, $input);
    }

    /**
     * @param array{expires_at:int,attempts_left:int} $meta
     */
    private function decrementAttempts(string $key, array $meta): void
    {
        $_SESSION['_pfg_captcha_meta'][$key]['attempts_left'] = $meta['attempts_left'] - 1;
    }

    private function clearChallenge(string $key): void
    {
        unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $started = @session_start();

        if ($started !== true && session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Unable to start session for captcha management.');
        }
    }
}

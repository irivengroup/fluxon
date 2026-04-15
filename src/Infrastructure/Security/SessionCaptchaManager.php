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

        $_SESSION['_pfg_captcha'][$key] = $code;
        $_SESSION['_pfg_captcha_meta'][$key] = [
            'expires_at' => time() + max(30, $this->ttlSeconds),
            'attempts_left' => max(1, $this->maxAttempts),
        ];

        return $code;
    }

    public function isCodeValid(string $key, ?string $input): bool
    {
        if ($this->isMissingInput($input)) {
            return false;
        }

        $expected = $_SESSION['_pfg_captcha'][$key] ?? null;
        $meta = $this->metaForKey($key);

        if (!$this->hasUsableChallenge($expected, $meta)) {
            $this->clearChallenge($key);

            return false;
        }

        if ($this->shouldExpireChallenge($meta)) {
            $this->clearChallenge($key);

            return false;
        }

        if ($this->matchesExpectedCode($expected, $input)) {
            $this->clearChallenge($key);

            return true;
        }

        $this->decrementAttempts($key, $meta);

        return false;
    }


private function isMissingInput(?string $input): bool
{
    return $input === null || $input === '';
}

/**
 * @param array{expires_at:int,attempts_left:int}|null $meta
 */
private function hasUsableChallenge(mixed $expected, ?array $meta): bool
{
    return is_string($expected) && $meta !== null;
}

/**
 * @param array{expires_at:int,attempts_left:int} $meta
 */
private function shouldExpireChallenge(array $meta): bool
{
    return $this->isExpired($meta) || $this->hasNoAttemptsLeft($meta);
}

private function matchesExpectedCode(string $expected, string $input): bool
{
    return hash_equals($expected, $input);
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

    private function clearChallenge(string $key): void
    {
        unset($_SESSION['_pfg_captcha'][$key], $_SESSION['_pfg_captcha_meta'][$key]);
    }

    /**
     * @return array{expires_at:int,attempts_left:int}|null
     */
    private function metaForKey(string $key): ?array
    {
        $meta = $_SESSION['_pfg_captcha_meta'][$key] ?? null;

        return $this->hasValidMeta($meta) ? $meta : null;
    }

    private function hasValidMeta(mixed $meta): bool
    {
        return is_array($meta)
            && isset($meta['expires_at'], $meta['attempts_left'])
            && is_int($meta['expires_at'])
            && is_int($meta['attempts_left']);
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

    /**
     * @param array{expires_at:int,attempts_left:int} $meta
     */
    private function decrementAttempts(string $key, array $meta): void
    {
        $meta['attempts_left']--;

        if ($meta['attempts_left'] <= 0) {
            $this->clearChallenge($key);

            return;
        }

        $_SESSION['_pfg_captcha_meta'][$key] = $meta;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Infrastructure\Security\SessionCaptchaManager;
use PHPUnit\Framework\TestCase;

final class SessionCaptchaManagerHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function testGeneratedCodeContainsUppercaseAndLowercaseCharacters(): void
    {
        $manager = new SessionCaptchaManager();
        $code = $manager->generateCode('captcha.mixed', 5, 8);

        self::assertMatchesRegularExpression('/[A-Z]/', $code);
        self::assertMatchesRegularExpression('/[a-z]/', $code);
    }

    public function testChallengeIsInvalidatedAfterConfiguredAttempts(): void
    {
        $manager = new SessionCaptchaManager(300, 2);
        $code = $manager->generateCode('captcha.attempts', 5, 5);

        self::assertFalse($manager->isCodeValid('captcha.attempts', 'wrong'));
        self::assertFalse($manager->isCodeValid('captcha.attempts', 'wrong-again'));
        self::assertFalse($manager->isCodeValid('captcha.attempts', $code));
    }

    public function testExpiredChallengeFailsValidation(): void
    {
        $manager = new SessionCaptchaManager(30, 5);
        $code = $manager->generateCode('captcha.expired', 5, 5);

        $_SESSION['_pfg_captcha_meta']['captcha.expired']['expires_at'] = time() - 1;

        self::assertFalse($manager->isCodeValid('captcha.expired', $code));
    }
}

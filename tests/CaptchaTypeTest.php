<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Domain\Form\FormView;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use PHPUnit\Framework\TestCase;

final class CaptchaTypeTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function testCaptchaRendersAndValidatesCaseSensitively(): void
    {
        $invalidForm = $this->createCaptchaForm();
        $invalidView = $invalidForm->createView();
        $this->assertCaptchaViewIsRenderable($invalidView);

        $invalidExpected = $this->expectedCaptchaCode();
        $invalidForm->handleRequest(new ArrayRequest('POST', [
            'secure' => [
                '_token' => $this->csrfTokenFromView($invalidView),
                'captcha' => strtolower($invalidExpected),
            ],
        ]));

        self::assertTrue($invalidForm->isSubmitted());
        self::assertFalse($invalidForm->isValid());

        $validForm = $this->createCaptchaForm();
        $validView = $validForm->createView();
        $validExpected = $this->expectedCaptchaCode();
        $validForm->handleRequest(new ArrayRequest('POST', [
            'secure' => [
                '_token' => $this->csrfTokenFromView($validView),
                'captcha' => $validExpected,
            ],
        ]));

        self::assertTrue($validForm->isSubmitted());
        self::assertTrue($validForm->isValid());
    }

    private function createCaptchaForm(): \Iriven\PhpFormGenerator\Domain\Form\Form
    {
        return (new FormGenerator('secure'))
            ->open(['method' => 'POST'])
            ->addCaptcha('captcha', ['min_length' => 5, 'max_length' => 8])
            ->getForm();
    }

    private function assertCaptchaViewIsRenderable(FormView $view): void
    {
        $captchaView = $this->childByName($view, 'captcha');

        self::assertNotNull($captchaView);
        self::assertIsArray($captchaView->vars);
        self::assertArrayHasKey('captcha_svg', $captchaView->vars);
        self::assertIsString($captchaView->vars['captcha_svg']);
    }

    private function csrfTokenFromView(FormView $view): ?string
    {
        $tokenView = $this->childByName($view, '_token');

        return $tokenView !== null && is_string($tokenView->value) ? $tokenView->value : null;
    }

    private function expectedCaptchaCode(): string
    {
        $expected = $_SESSION['_pfg_captcha']['secure.captcha'] ?? null;
        self::assertIsString($expected);

        if (!is_string($expected)) {
            self::fail('Expected captcha code was not generated.');
        }

        return $expected;
    }

    private function childByName(FormView $view, string $name): ?FormView
    {
        foreach ($view->children as $child) {
            if ($child->name === $name) {
                return $child;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormType\InvoiceType;
use Iriven\PhpFormGenerator\Application\FormType\RegistrationType;
use PHPUnit\Framework\TestCase;

final class ApplicationFormTypesTest extends TestCase
{
    public function testInvoiceTypeBuildsAndRenders(): void
    {
        $factory = new FormFactory();
        $form = $factory->create(InvoiceType::class, [], ['name' => 'invoice']);
        $view = $form->createView();

        self::assertArrayHasKey('customer', $view->children);
        self::assertArrayHasKey('issuedAt', $view->children);
        self::assertArrayHasKey('items', $view->children);
        self::assertSame('invoice', $view->name);
    }

    public function testRegistrationTypeBuildsAndRenders(): void
    {
        $factory = new FormFactory();
        $form = $factory->create(RegistrationType::class, [], ['name' => 'registration']);
        $view = $form->createView();

        self::assertArrayHasKey('email', $view->children);
        self::assertArrayHasKey('password', $view->children);
        self::assertArrayHasKey('confirmPassword', $view->children);
        self::assertArrayHasKey('acceptTerms', $view->children);
        self::assertArrayHasKey('captcha', $view->children);
    }
}

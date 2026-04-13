<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\Type\ContactType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class FormWorkflowTest extends TestCase
{
    public function testContactFormValidSubmission(): void
    {
        $factory = new FormFactory(new NullCsrfManager());
        $form = $factory->create(ContactType::class, null, 'contact', [
            'method' => 'POST',
            'csrf_protection' => true,
            'csrf_token_id' => 'contact',
        ]);

        $request = new ArrayRequest('POST', [
            '_token' => 'dev-token',
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'country' => 'FR',
            'message' => 'I need a custom enterprise form workflow.',
        ]);

        $form->handleRequest($request);

        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isValid());
        self::assertIsArray($form->data());
        self::assertSame('Ada Lovelace', $form->data()['name']);

        $html = (new HtmlRenderer())->render($form);
        self::assertStringContainsString('<fieldset', $html);
        self::assertStringContainsString('name="country"', $html);
    }

    public function testInvalidEmailTriggersError(): void
    {
        $factory = new FormFactory();
        $form = $factory->create(ContactType::class, null, 'contact', ['method' => 'POST']);
        $form->handleRequest(new ArrayRequest('POST', [
            'name' => 'Ada',
            'email' => 'not-an-email',
            'message' => '1234567890',
        ]));

        self::assertFalse($form->isValid());
        self::assertNotEmpty($form->field('email')?->errors);
    }
}

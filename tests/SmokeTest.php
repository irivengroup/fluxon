<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ArrayDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function testFormSubmissionAndRender(): void
    {
        $factory = new FormFactory(new ArrayDataMapper(), new NullCsrfManager());

        $form = $factory->createBuilder('contact')
            ->add('name', TextType::class, ['constraints' => [new Required()]])
            ->add('email', EmailType::class)
            ->add('country', CountryType::class)
            ->add('attachment', FileType::class, ['required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Send'])
            ->getForm();

        $form->handleRequest(new ArrayRequest([
            'contact' => [
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'country' => 'FR',
                '_token' => $form->getCsrfToken(),
            ],
        ]));

        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isValid());

        $html = (new HtmlRenderer())->renderForm($form->createView());
        self::assertStringContainsString('<form', $html);
        self::assertStringContainsString('alice@example.com', $html);
    }
}

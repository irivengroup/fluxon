<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormHookKernel;
use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Tests\Fixtures\Hook\ThrowingHook;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HookIndustrializationRuntimeTest extends TestCase
{
    public function testMultipleHooksRunInRegistrationOrder(): void
    {
        $messages = [];
        $kernel = new FormHookKernel();
        $kernel->register(new class(&$messages) implements FormHookInterface {
            public function __construct(private array &$messages) {}
            public static function getName(): string { return 'post_submit'; }
            public function __invoke(Form $form, array $context = []): void { $this->messages[] = 'first'; }
        });
        $kernel->register(new class(&$messages) implements FormHookInterface {
            public function __construct(private array &$messages) {}
            public static function getName(): string { return 'post_submit'; }
            public function __invoke(Form $form, array $context = []): void { $this->messages[] = 'second'; }
        });

        $factory = new FormFactory(hookKernel: $kernel);
        $builder = $factory->createBuilder('demo');
        $builder->add('name', 'TextType');
        $form = $builder->getForm();
        $form->handleRequest(new ArrayRequest('POST', ['demo' => ['name' => 'Alice']]));

        self::assertSame(['first', 'second'], $messages);
    }

    public function testHookExceptionCanBubbleByDefault(): void
    {
        $factory = new FormFactory(hookKernel: (new FormHookKernel())->register(new ThrowingHook()));
        $builder = $factory->createBuilder('demo');
        $builder->add('name', 'TextType');
        $form = $builder->getForm();

        $this->expectException(RuntimeException::class);
        $form->handleRequest(new ArrayRequest('POST', ['demo' => ['name' => 'Alice']]));
    }

    public function testHookExceptionCanBeSwallowedWhenConfigured(): void
    {
        $factory = new FormFactory(hookKernel: (new FormHookKernel(true))->register(new ThrowingHook()));
        $builder = $factory->createBuilder('demo');
        $builder->add('name', 'TextType');
        $form = $builder->getForm();

        $form->handleRequest(new ArrayRequest('POST', ['demo' => ['name' => 'Alice']]));

        self::assertArrayHasKey('_form', $form->getErrors());
        self::assertStringContainsString('Hook failure', $form->getErrors()['_form'][0]);
    }
}

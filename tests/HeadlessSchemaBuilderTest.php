<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormRuntimeContext;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class HeadlessSchemaBuilderTest extends TestCase
{
    public function testHeadlessSchemaContainsFormFieldsUiRuntimeAndValidation(): void
    {
        $builder = (new FormFactory())->createBuilder('contact', null, ['method' => 'POST', 'action' => '/contact']);
        $builder->add('name', 'TextType', ['required' => true, 'label' => 'Name', 'group' => 'main', 'order' => 1]);
        $form = $builder->getForm();

        $runtime = new FormRuntimeContext($form, 'tailwind', 'RendererClass', ['variant' => 'compact']);
        $schema = (new FormSchemaManager(new ArraySchemaExporter()))->exportHeadless($form, $runtime);

        self::assertArrayHasKey('form', $schema);
        self::assertArrayHasKey('fields', $schema);
        self::assertArrayHasKey('ui', $schema);
        self::assertArrayHasKey('runtime', $schema);
        self::assertArrayHasKey('validation', $schema);
        self::assertSame('contact', $schema['form']['name']);
        self::assertSame('input:text', $schema['fields'][0]['component']);
        self::assertTrue($schema['validation']['name']['required']);
    }
}

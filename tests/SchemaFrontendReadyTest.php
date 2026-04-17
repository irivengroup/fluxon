<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormRuntimeContext;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class SchemaFrontendReadyTest extends TestCase
{
    public function testSchemaIncludesUiAndPayloadMetadata(): void
    {
        $builder = (new FormFactory())->createBuilder('contact');
        $builder->add('name', 'TextType');
        $form = $builder->getForm();

        $context = new FormRuntimeContext($form, 'tailwind', 'RendererClass', ['variant' => 'compact']);
        $schema = (new FormSchemaManager(new ArraySchemaExporter()))->export($form, $context);

        self::assertArrayHasKey('runtime', $schema);
        self::assertArrayHasKey('payload', $schema['runtime']);
        self::assertArrayHasKey('ui', $schema);
        self::assertSame('compact', $schema['ui']['variant']);
    }
}

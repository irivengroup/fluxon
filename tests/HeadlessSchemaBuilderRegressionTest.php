<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class HeadlessSchemaBuilderRegressionTest extends TestCase
{
    public function testHeadlessSchemaWithoutRuntimeContextStillBuildsConsistently(): void
    {
        $builder = (new FormFactory())->createBuilder('contact', null, ['method' => 'POST']);
        $builder->add('name', 'TextType', ['required' => false]);
        $schema = (new FormSchemaManager(new ArraySchemaExporter()))->exportHeadless($builder->getForm());

        self::assertArrayHasKey('form', $schema);
        self::assertArrayHasKey('fields', $schema);
        self::assertArrayHasKey('ui', $schema);
        self::assertArrayHasKey('runtime', $schema);
        self::assertArrayHasKey('validation', $schema);
        self::assertSame([], $schema['runtime']);
    }

    public function testHeadlessSchemaSupportsMissingLayoutMetadata(): void
    {
        $builder = (new FormFactory())->createBuilder('contact');
        $builder->add('name', 'TextType', ['label' => 'Name']);
        $schema = (new FormSchemaManager(new ArraySchemaExporter()))->exportHeadless($builder->getForm());

        self::assertNull($schema['fields'][0]['layout']['group']);
        self::assertNull($schema['fields'][0]['layout']['order']);
    }
}

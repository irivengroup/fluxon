<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class SchemaIndexRegressionTest extends TestCase
{
    public function testExportContainsStableTopLevelKeys(): void
    {
        $factory = new FormFactory();
        $builder = $factory->createBuilder('contact', null, ['method' => 'POST', 'action' => '/contact']);
        $builder->add('name', 'TextType', ['required' => true]);
        $schema = (new FormSchemaManager(new ArraySchemaExporter()))->export($builder->getForm());

        self::assertSame(['name', 'method', 'action', 'fields'], array_keys($schema));
        self::assertSame('/contact', $schema['action']);
    }
}

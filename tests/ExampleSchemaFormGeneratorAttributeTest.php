<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Generation\ExampleSchemaFormGenerator;
use Iriven\PhpFormGenerator\Domain\Attribute\FormField;
use PHPUnit\Framework\TestCase;

final class ExampleSchemaFormGeneratorAttributeTest extends TestCase
{
    public function testGeneratorReturnsMetadataRichSchema(): void
    {
        $dto = new class {
            #[FormField(type: 'EmailType', required: true, label: 'Email')]
            public string $email = 'john@example.com';
        };

        $schema = (new ExampleSchemaFormGenerator())->generate($dto);

        self::assertSame('EmailType', $schema['fields']['email']['type']);
        self::assertTrue($schema['fields']['email']['required']);
        self::assertSame('Email', $schema['fields']['email']['label']);
    }
}

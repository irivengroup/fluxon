<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Generation\ExampleSchemaFormGenerator;
use PHPUnit\Framework\TestCase;

final class ExampleSchemaFormGeneratorTest extends TestCase
{
    public function testGenerateSchemaFromSample(): void
    {
        $schema = (new ExampleSchemaFormGenerator())->generate(['email' => 'john@example.com']);

        self::assertSame('TextType', $schema['fields']['email']['type']);
    }
}

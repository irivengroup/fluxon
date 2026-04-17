<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\ValidationExporter;
use Iriven\PhpFormGenerator\Domain\Form\FieldConfig;
use PHPUnit\Framework\TestCase;

final class ValidationExporterTest extends TestCase
{
    public function testValidationExporterIncludesRequiredRule(): void
    {
        $field = new FieldConfig('name', 'TextType', ['required' => true]);
        $rules = (new ValidationExporter())->export($field);

        self::assertTrue($rules['required']);
    }
}

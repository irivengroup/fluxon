<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Frontend\ValidationExporter;
use Iriven\PhpFormGenerator\Domain\Form\FieldConfig;
use PHPUnit\Framework\TestCase;

final class ValidationExporterRegressionTest extends TestCase
{
    public function testValidationExporterHandlesFieldWithoutConstraints(): void
    {
        $field = new FieldConfig('name', 'TextType', []);
        $rules = (new ValidationExporter())->export($field);

        self::assertFalse($rules['required']);
        self::assertArrayNotHasKey('type', $rules);
    }
}

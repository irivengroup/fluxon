<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Application\Frontend\FrontendFrameworkPresets;
use Iriven\PhpFormGenerator\Application\Frontend\FrontendSdk;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;
use PHPUnit\Framework\TestCase;

final class FrontendSdkMaintenanceTest extends TestCase
{
    public function testSchemaIsNormalizedWhenRuntimeContextIsNull(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $sdk = new FrontendSdk(new FormSchemaManager(new ArraySchemaExporter()), FrontendFrameworkPresets::react());

        $schema = $sdk->buildSchema($form, null);

        self::assertArrayHasKey('form', $schema);
        self::assertArrayHasKey('fields', $schema);
        self::assertArrayHasKey('ui', $schema);
        self::assertArrayHasKey('runtime', $schema);
        self::assertArrayHasKey('validation', $schema);
    }

    public function testPayloadIsNormalizedWhenDataIsEmpty(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $sdk = new FrontendSdk(new FormSchemaManager(new ArraySchemaExporter()), FrontendFrameworkPresets::vue());

        $payload = $sdk->buildSubmissionPayload($form, []);

        self::assertSame([], $payload['payload']);
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\Headless\HeadlessFormProcessor;
use Iriven\PhpFormGenerator\Application\Headless\HeadlessFormState;
use Iriven\PhpFormGenerator\Application\Headless\HeadlessResponseBuilder;
use PHPUnit\Framework\TestCase;
final class HeadlessRegressionTest extends TestCase
{
    public function testEmptyPayloadAndErrorsRemainStable(): void
    {
        $payload = (new HeadlessResponseBuilder())->build(new HeadlessFormState(false, false, [], [], []));
        self::assertSame([], $payload['payload']);
        self::assertSame([], $payload['errors']);
        self::assertSame([], $payload['metadata']);
    }
    public function testStructuredErrorsRemainSerializable(): void
    {
        $result = (new HeadlessResponseBuilder())->build(new HeadlessFormState(true, false, ['email' => 'bad'], ['email' => ['Invalid email'], 'global' => 'Failure'], ['mode' => 'submit']));
        self::assertSame(['Invalid email'], $result['errors']['email']);
        self::assertSame('Failure', $result['errors']['global']);
    }
    public function testHeadlessSchemaRemainsVersioned(): void
    {
        $form = (new FormFactory())->createBuilder('contact')->getForm();
        $schema = (new HeadlessFormProcessor())->schema($form);
        self::assertArrayHasKey('schema', $schema);
        self::assertArrayHasKey('version', $schema['schema']);
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\Headless\HeadlessFormState;
use Iriven\PhpFormGenerator\Application\Headless\HeadlessResponseBuilder;
use PHPUnit\Framework\TestCase;
final class HeadlessJsonContractShapeTest extends TestCase
{
    public function testJsonFirstTopLevelKeysRemainStable(): void
    {
        $data = (new HeadlessResponseBuilder())->build(new HeadlessFormState(true, true, [], [], ['mode' => 'submit']));
        self::assertSame(['state', 'payload', 'errors', 'metadata'], array_keys($data));
        self::assertSame(['submitted', 'valid'], array_keys($data['state']));
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\Mapping\ObjectFormMapper;
use PHPUnit\Framework\TestCase;
final class ObjectFormMapperTest extends TestCase
{
    public function testArrayPayloadCanHydrateArrayTarget(): void
    {
        $data = (new ObjectFormMapper())->hydrate(['email' => 'john@example.com'], ['name' => 'John']);
        self::assertSame('john@example.com', $data['email']);
        self::assertSame('John', $data['name']);
    }
    public function testObjectCanBeExtractedToArray(): void
    {
        $object = new class { public string $email = 'john@example.com'; public string $name = 'John'; };
        $data = (new ObjectFormMapper())->extract($object);
        self::assertSame('john@example.com', $data['email']);
        self::assertSame('John', $data['name']);
    }
}

<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Tests;
use Iriven\PhpFormGenerator\Application\Mapping\FormHydrator;
use PHPUnit\Framework\TestCase;
final class FormHydratorTest extends TestCase
{
    public function testHydratorNormalizesPropertyNames(): void
    {
        $data = (new FormHydrator())->hydrate([' email ' => 'john@example.com']);
        self::assertSame('john@example.com', $data['email']);
    }
    public function testHydratorKeepsNestedArrays(): void
    {
        $data = (new FormHydrator())->hydrate(['profile' => ['city' => 'Paris']]);
        self::assertSame('Paris', $data['profile']['city']);
    }
}

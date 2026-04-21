<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Generation\DtoFormGuesser;
use PHPUnit\Framework\TestCase;

final class DtoFormGuesserTest extends TestCase
{
    public function testGuessTypesFromArray(): void
    {
        $fields = (new DtoFormGuesser())->guess([
            'email' => 'john@example.com',
            'age' => 30,
            'active' => true,
            'tags' => ['a'],
        ]);

        self::assertSame('TextType', $fields['email']);
        self::assertSame('NumberType', $fields['age']);
        self::assertSame('CheckboxType', $fields['active']);
        self::assertSame('CollectionType', $fields['tags']);
    }
}

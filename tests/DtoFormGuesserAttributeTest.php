<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Generation\DtoFormGuesser;
use Iriven\PhpFormGenerator\Domain\Attribute\FormField;
use Iriven\PhpFormGenerator\Domain\Attribute\FormIgnore;
use PHPUnit\Framework\TestCase;

final class DtoFormGuesserAttributeTest extends TestCase
{
    public function testAttributeOverridesFallbackInference(): void
    {
        $dto = new class {
            #[FormField(type: 'EmailType', required: true, label: 'Email')]
            public string $email = 'john@example.com';
        };

        $fields = (new DtoFormGuesser())->guess($dto);

        self::assertSame('EmailType', $fields['email']['type']);
        self::assertTrue($fields['email']['required']);
        self::assertSame('Email', $fields['email']['label']);
    }

    public function testIgnoredFieldIsSkipped(): void
    {
        $dto = new class {
            public string $email = 'john@example.com';

            #[FormIgnore]
            public string $internal = 'secret';
        };

        $fields = (new DtoFormGuesser())->guess($dto);

        self::assertArrayHasKey('email', $fields);
        self::assertArrayNotHasKey('internal', $fields);
    }
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\Generation\DtoAttributeReader;
use Iriven\PhpFormGenerator\Domain\Attribute\FormField;
use Iriven\PhpFormGenerator\Domain\Attribute\FormIgnore;
use PHPUnit\Framework\TestCase;

final class DtoAttributeReaderTest extends TestCase
{
    public function testReaderExtractsFieldMetadataAndIgnore(): void
    {
        $dto = new class {
            #[FormField(type: 'EmailType', required: true, label: 'Email')]
            public string $email = 'john@example.com';

            #[FormIgnore]
            public string $internal = 'secret';
        };

        $data = (new DtoAttributeReader())->read($dto);

        self::assertSame('EmailType', $data['email']['type']);
        self::assertTrue($data['email']['required']);
        self::assertSame('Email', $data['email']['label']);
        self::assertTrue($data['internal']['ignored']);
    }
}

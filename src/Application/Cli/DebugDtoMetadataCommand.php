<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Generation\DtoAttributeReader;
use Iriven\PhpFormGenerator\Domain\Attribute\FormField;
use Iriven\PhpFormGenerator\Domain\Attribute\FormIgnore;

/** @api */
final class DebugDtoMetadataCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'debug:dto-metadata';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $dto = new class {
            #[FormField(type: 'EmailType', required: true, label: 'Email')]
            public string $email = 'john@example.com';

            #[FormIgnore]
            public string $internal = 'secret';
        };

        $data = (new DtoAttributeReader())->read($dto);

        return json_encode($data, JSON_PRETTY_PRINT) ?: '{}';
    }
}

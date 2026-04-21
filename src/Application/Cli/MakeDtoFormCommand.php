<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Generation\ExampleSchemaFormGenerator;

/** @api */
final class MakeDtoFormCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'make:dto-form';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $schema = (new ExampleSchemaFormGenerator())->generate([
            'email' => 'john@example.com',
            'age' => 30,
            'active' => true,
        ]);

        return json_encode($schema, JSON_PRETTY_PRINT) ?: '{}';
    }
}

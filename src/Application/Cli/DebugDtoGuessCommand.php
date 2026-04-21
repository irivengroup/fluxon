<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Generation\DtoFormGuesser;

/** @api */
final class DebugDtoGuessCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'debug:dto-guess';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $data = (new DtoFormGuesser())->guess([
            'email' => 'john@example.com',
            'age' => 30,
            'active' => true,
        ]);

        return json_encode($data, JSON_PRETTY_PRINT) ?: '{}';
    }
}

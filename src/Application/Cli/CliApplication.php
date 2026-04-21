<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

/** @api */
final class CliApplication
{
    /** @var array<string, CliCommandInterface> */
    private array $commands = [];

    public function register(CliCommandInterface $command): self
    {
        $this->commands[$command->name()] = $command;

        return $this;
    }

    public function run(string $name, array $args = []): string
    {
        if (!isset($this->commands[$name])) {
            return sprintf('Unknown command: %s', $name);
        }

        return $this->commands[$name]->run($args);
    }

    /**
     * @return array<int, string>
     */
    public function commands(): array
    {
        $names = array_keys($this->commands);
        sort($names);

        return array_values($names);
    }
}

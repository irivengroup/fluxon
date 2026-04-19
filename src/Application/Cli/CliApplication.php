<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

/**
 * @api
 */
final class CliApplication
{
    /** @var array<string, CliCommandInterface> */
    private array $commands = [];

    /**
     * @param array<int, CliCommandInterface> $commands
     */
    public function __construct(array $commands = [])
    {
        foreach ($commands as $command) {
            $this->register($command);
        }
    }

    public function register(CliCommandInterface $command): self
    {
        $this->commands[$command->name()] = $command;

        return $this;
    }

    /**
     * @param array<int, string> $args
     */
    public function run(string $name, array $args = []): string
    {
        if (!isset($this->commands[$name])) {
            return sprintf('Unknown command: %s', $name);
        }

        return $this->commands[$name]->run($args);
    }

    /**
     * @return list<string>
     */
    public function commands(): array
    {
        return array_keys($this->commands);
    }
}

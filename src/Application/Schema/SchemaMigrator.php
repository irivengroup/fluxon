<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Schema;

/**
 * @api
 */
final class SchemaMigrator
{
    /** @var array<int, SchemaMigrationInterface> */
    private array $migrations = [];

    /**
     * @param array<int, SchemaMigrationInterface> $migrations
     */
    public function __construct(array $migrations = [])
    {
        $this->migrations = $migrations;
    }

    public function register(SchemaMigrationInterface $migration): self
    {
        $this->migrations[] = $migration;

        return $this;
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    public function migrate(array $schema, string $fromVersion, string $toVersion): array
    {
        $current = $schema;

        foreach ($this->migrations as $migration) {
            if ($migration->supports($fromVersion, $toVersion)) {
                $current = $migration->migrate($current);
            }
        }

        return $current;
    }
}

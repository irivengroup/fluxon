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
    public function migrate(array $schema, string $targetVersion, ?string $fromVersion = null): array
    {
        $currentVersion = $fromVersion ?? (string) (($schema['schema']['version'] ?? ''));

        if ($currentVersion === $targetVersion) {
            return $schema;
        }

        foreach ($this->migrations as $migration) {
            if ($migration->fromVersion() !== $currentVersion) {
                continue;
            }

            if ($migration->toVersion() !== $targetVersion) {
                continue;
            }

            $migrated = $migration->migrate($schema);
            $migrated['schema']['version'] = $targetVersion;

            return $migrated;
        }

        return $schema;
    }
}

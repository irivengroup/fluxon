<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

/**
 * @api
 */
final class FrontendSdkConfig
{
    /**
     * @param array<string, mixed> $defaults
     */
    public function __construct(
        private readonly string $framework = 'generic',
        private readonly string $schemaVersion = '2.0',
        private readonly array $defaults = [],
    ) {
    }

    public function framework(): string
    {
        return $this->framework;
    }

    public function schemaVersion(): string
    {
        return $this->schemaVersion;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return $this->defaults;
    }
}

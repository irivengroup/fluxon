<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Observability;

/** @api */
final class RuntimeMetrics
{
    /**
     * @param array<string, float|int|bool|string> $values
     */
    public function __construct(
        private readonly array $values = [],
    ) {
    }

    /**
     * @return array<string, float|int|bool|string>
     */
    public function values(): array
    {
        return $this->values;
    }
}

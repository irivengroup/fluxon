<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Mapping;

/** @api */
final class FormExtractionManager
{
    public function __construct(
        private readonly ObjectFormMapper $mapper = new ObjectFormMapper(),
    ) {}

    /**
     * @param object|array<string, mixed> $source
     * @return array<string, mixed>
     */
    public function extract(object|array $source): array
    {
        return $this->mapper->extract($source);
    }
}

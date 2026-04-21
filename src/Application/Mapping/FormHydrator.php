<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Mapping;

/** @api */
final class FormHydrator
{
    public function __construct(
        private readonly ObjectFormMapper $mapper = new ObjectFormMapper(),
        private readonly PropertyPathNormalizer $normalizer = new PropertyPathNormalizer(),
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @param mixed $target
     * @return array<string, mixed>
     */
    public function hydrate(array $payload, mixed $target = []): array
    {
        if ($payload === []) {
            return $this->mapper->extract($target);
        }

        $normalized = [];
        foreach ($payload as $key => $value) {
            $normalized[$this->normalizer->normalize((string) $key)] = $value;
        }

        return $this->mapper->hydrate($normalized, $target);
    }
}

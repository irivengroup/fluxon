<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

final class FormGeneratorOpenNormalizer
{
    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    public function normalize(array $attributes, array $options): array
    {
        $formAttributes = $attributes;
        $configurationOptions = $options;

        foreach (['method', 'action'] as $key) {
            if (array_key_exists($key, $formAttributes)) {
                $configurationOptions[$key] = $formAttributes[$key];
                unset($formAttributes[$key]);
            }
        }

        return [$formAttributes, $configurationOptions];
    }
}

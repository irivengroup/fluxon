<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class CountryType extends SelectType
{
    /**
     * @param array<string, mixed> $options
     * @return array<string, string>
     */
    public static function choices(array $options = []): array
    {
        $provider = new CountryProvider();
        $countries = $provider->all();
        $region = self::normalizedRegion($options);

        if ($region !== null) {
            $countries = array_intersect_key($countries, array_flip($provider->regionCodes($region)));
        }

        if ((bool) ($options['sort'] ?? false)) {
            asort($countries, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return $countries;
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function normalizedRegion(array $options): ?string
    {
        if (!isset($options['region']) || !is_string($options['region'])) {
            return null;
        }

        $region = strtolower(trim($options['region']));

        return $region !== '' ? $region : null;
    }
}

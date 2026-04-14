<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

class CountryType extends SelectType
{
    /** @return array<string, string> */
    public static function choices(): array
    {
        return [
            'FR' => 'France',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'CA' => 'Canada',
            'DE' => 'Germany',
            'ES' => 'Spain',
            'GB' => 'United Kingdom',
            'IT' => 'Italy',
            'US' => 'United States',
        ];
    }
}

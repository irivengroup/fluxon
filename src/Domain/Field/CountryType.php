<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class CountryType extends AbstractFieldType
{
    public function getBlockPrefix(): string
    {
        return 'country';
    }

    public function configureOptions(array $options = []): array
    {
        $defaults = parent::configureOptions($options);
        $defaults['choices'] = $defaults['choices'] ?: self::countries();
        $defaults['placeholder'] = $defaults['placeholder'] ?? 'Select a country';
        return $defaults;
    }

    private static function countries(): array
    {
        return [
            'Afghanistan' => 'AF','Albania' => 'AL','Algeria' => 'DZ','Argentina' => 'AR','Australia' => 'AU',
            'Austria' => 'AT','Belgium' => 'BE','Brazil' => 'BR','Canada' => 'CA','China' => 'CN',
            'Denmark' => 'DK','Egypt' => 'EG','Finland' => 'FI','France' => 'FR','Germany' => 'DE',
            'Greece' => 'GR','India' => 'IN','Ireland' => 'IE','Italy' => 'IT','Japan' => 'JP',
            'Luxembourg' => 'LU','Mexico' => 'MX','Morocco' => 'MA','Netherlands' => 'NL','Norway' => 'NO',
            'Poland' => 'PL','Portugal' => 'PT','Romania' => 'RO','Spain' => 'ES','Sweden' => 'SE',
            'Switzerland' => 'CH','Tunisia' => 'TN','Turkey' => 'TR','United Kingdom' => 'GB','United States' => 'US'
        ];
    }
}
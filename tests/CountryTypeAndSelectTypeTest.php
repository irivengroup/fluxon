<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\SelectType;
use PHPUnit\Framework\TestCase;

final class CountryTypeAndSelectTypeTest extends TestCase
{
    public function testCountryTypeExtendsSelectType(): void
    {
        self::assertTrue(is_subclass_of(CountryType::class, SelectType::class));
    }

    public function testCountryTypeCanBeRegionFilteredAndSorted(): void
    {
        $choices = CountryType::choices([
            'region' => 'europe',
            'sort' => true,
        ]);

        self::assertArrayHasKey('FR', $choices);
        self::assertArrayNotHasKey('US', $choices);
        self::assertSame('France', $choices['FR']);
    }
}

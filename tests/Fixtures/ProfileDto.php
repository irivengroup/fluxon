<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures;

final class ProfileDto
{
    public string $name = '';
    public AddressDto $address;
    /** @var list<AddressDto> */
    public array $addresses = [];
    public bool $active = false;

    public function __construct()
    {
        $this->address = new AddressDto();
    }
}

final class AddressDto
{
    public string $street = '';
    public string $city = '';
}

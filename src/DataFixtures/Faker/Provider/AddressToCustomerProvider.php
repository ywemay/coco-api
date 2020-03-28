<?php
namespace App\DataFixtures\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

final class AddressToCustomerProvider extends BaseProvider
{
    public function addressToCustomer($address)
    {
        return $address->getCustomerProfile();
    }
}

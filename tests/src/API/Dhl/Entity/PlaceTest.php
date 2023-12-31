<?php

declare(strict_types=1);

namespace Drupal\Tests\location_finder\API\Dhl\Entity;

use Drupal\location_finder\API\Dhl\Entity\Address;
use Drupal\location_finder\API\Dhl\Entity\Place;
use Drupal\Tests\UnitTestCase;

class PlaceTest extends UnitTestCase
{
    public function testCreatePlaceEntity(): void
    {
        $address = (new Address())
            ->setCountryCode('DE')
            ->setPostalCode('53113')
            ->setAddressLocality('Bonn')
            ->setStreetAddress('Charles-de-Gaulle-Str. 20')
        ;
        $place = (new Place())
            ->setAddress($address)
        ;

        $this->assertEquals('DE', $place->getAddress()->getCountryCode());
    }
}

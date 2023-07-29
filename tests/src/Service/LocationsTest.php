<?php

declare(strict_types=1);

namespace Drupal\Tests\dhl_location_finder\Service;

use Drupal\dhl_location_finder\API\Client;
use Drupal\dhl_location_finder\API\DTO\LocationsDTO;
use Drupal\dhl_location_finder\API\LocationProvider;
use Drupal\dhl_location_finder\Entity\Location;
use Drupal\dhl_location_finder\Middleware\AddressFilterMiddleware;
use Drupal\dhl_location_finder\Middleware\LocationHandler;
use Drupal\dhl_location_finder\Middleware\WeekendFilterMiddleware;
use Drupal\dhl_location_finder\Service\Locations;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LocationsTest extends UnitTestCase
{
    private Locations $service;
    protected function setUp(): void
    {
        parent::setUp();

        $guzzleClient = new \GuzzleHttp\Client();
        $apiClient = new Client($guzzleClient);
        $phpDocExtractor = new PhpDocExtractor();
        $typeExtractor   = new PropertyInfoExtractor(
            typeExtractors: [ new ConstructorExtractor([$phpDocExtractor]), $phpDocExtractor, new ReflectionExtractor()]
        );

        $serializer = new Serializer(
            normalizers: [
                new ObjectNormalizer(propertyTypeExtractor: $typeExtractor),
                new ArrayDenormalizer(),
            ],
            encoders: [new JsonEncoder(), new YamlEncoder()]
        );

        $locationProvider = new LocationProvider($apiClient, $serializer);
        $addressFilterMiddleware = new AddressFilterMiddleware();
        $weekendFilterMiddleware = new WeekendFilterMiddleware();
        $locationHandler = new LocationHandler($addressFilterMiddleware, $weekendFilterMiddleware);

        $this->service = new Locations($locationProvider, $locationHandler, $serializer);
    }

    public function testGetLocations(): void
    {
        $locationsDto = $this->service->findByAddress('DE', 'Bonn', '53113');

        $this->assertInstanceOf(LocationsDTO::class, $locationsDto);
    }

    public function testProcessLocations(): void
    {
        $locationsDto = $this->service->findByAddress('DE', 'Bonn', '53113');
        $locations = $this->service->processLocations($locationsDto->locations);

        $this->assertInstanceOf(Location::class, $locations[0]);
    }

    public function testConvertToYaml(): void
    {
        $locationsDto = $this->service->findByAddress('DE', 'Bonn', '53113');
        $locations = $this->service->processLocations($locationsDto->locations);
        $yaml = $this->service->convertToYaml($locations);

        $this->assertIsString($yaml);
    }
}

<?php

declare(strict_types=1);

namespace Drupal\dhl_location_finder\API;

use GuzzleHttp\ClientInterface;

class Client
{
    private const HOST = 'https://api-sandbox.dhl.com';
    private const API = 'location-finder/v1';

    public function __construct(private readonly ClientInterface $client)
    {
    }

    public function findByAddress(string $countryCode, string $addressLocality, string $postalCode): array
    {
        $options = [
            'query' => [
                'countryCode' => $countryCode,
                'addressLocality' => $addressLocality,
                'postalCode' => $postalCode,
            ],
            'headers'  => [
                'DHL-API-Key' => 'demo-key'
            ],
        ];

        $response = $this->client->get(self::HOST . '/' . self::API . '/find-by-address', $options);

        return json_decode($response->getBody()->getContents(), true);
    }
}
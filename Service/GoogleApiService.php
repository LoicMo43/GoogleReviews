<?php

namespace GoogleReviews\Service;

use GoogleReviews\GoogleReviews;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleApiService
{
    public function __construct(protected HttpClientInterface $client)
    {}

    public function checkValidConfiguration(): ?array
    {
        $apiKey = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_API_KEY);
        $location = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_PLACE_ID);

        try {
            $response = $this->client->request(
                'GET',
                GoogleReviews::GOOGLE_PLACES_API_URL . $location . "?fields=id,displayName&key=" . $apiKey
            );

            if ($response->getStatusCode() === 200) {
                return $response->toArray();
            }

        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
        }

        return null;
    }

    /**
     * Call API Google Maps
     * Return the reviews
     *
     * @param string|null $location
     * @param string $lang
     * @return array
     */
    public function getReviews(string $location = null, string $lang = 'fr'): array
    {
        $apiKey = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_API_KEY);

        if ($location === null) {
            $location = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_PLACE_ID);
        }

        try {
            $response = $this->client->request(
                'GET',
                GoogleReviews::GOOGLE_MAPS_API_URL . "?place_id=". $location . "&key=" . $apiKey . "&language=" . $lang ."&fields=review"
            );

            if ($response->getStatusCode() === 200) {
                $result = $response->toArray();

                return $result['result']['reviews'];
            }
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
        }

        return [];
    }
}
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
    public const DETAIL_URL = GoogleReviews::GOOGLE_MAPS_API_URL . DS ."details/json";
    public const FIND_PLACE_URL = GoogleReviews::GOOGLE_MAPS_API_URL . DS ."findplacefromtext/json";


    public function __construct(protected HttpClientInterface $client)
    {}

    /**
     * Call API Google Maps Details
     * Return the reviews
     *
     * @param string|null $location
     * @param string $lang
     * @param string $fields
     * @return array|null
     */
    public function getDetails(string $location = null, string $lang = 'fr', string $fields = ""): ?array
    {
        $apiKey = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_API_KEY);

        if ($fields !== "") {
            $fields = "&fields=" . $fields;
        }

        try {
            $response = $this->client->request(
                'GET',
                self::DETAIL_URL . "?place_id=". $location . "&key=" . $apiKey . "&language=" . $lang . $fields
            );

            if ($response->getStatusCode() === 200) {
                return $response->toArray()['result'];
            }

        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
        }

        return null;
    }

    /**
     * Call API Google Maps Find Place
     * Return possible place_id(s) of the location
     *
     * @param string $lat
     * @param string $lng
     * @param string $field
     * @return array|null
     */
    public function findPlaceId(string $lat, string $lng, string $field): ?array
    {
        $apiKey = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_API_KEY);

        try {
            $response = $this->client->request(
                'GET',
                self::FIND_PLACE_URL . "?input=" . $field. "&inputtype=textquery&locationbias=:radius@" . $lat . "," . $lng . "&key=" . $apiKey
            );

            if ($response->getStatusCode() === 200) {
                return $response->toArray()['candidates'];
            }
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
        }

        return [];
    }
}
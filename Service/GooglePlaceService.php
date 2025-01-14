<?php

namespace GoogleReviews\Service;

use Exception;
use GoogleReviews\GoogleReviews;

class GooglePlaceService
{
    public const GOOGLE_PlACES_PATH = THELIA_ROOT . 'public/google_reviews' . DS;
    public const GOOGLE_PlACES_FILE = self::GOOGLE_PlACES_PATH . 'place_id_';

    public function __construct(protected GoogleApiService $googleApiService)
    {}

    /**
     * @param string $placeId
     * @param string $locale
     * @param \DateTime $date
     * @return array|null
     * @throws \DateMalformedStringException
     * @throws \JsonException
     */
    public function saveDetailsJson(string $placeId, string $locale, \DateTime $date): ?array
    {
        $details = $this->googleApiService->getDetails($placeId, $locale);

        if (!is_dir(self::GOOGLE_PlACES_PATH) && !mkdir(self::GOOGLE_PlACES_PATH, 0775, true) && !is_dir(self::GOOGLE_PlACES_PATH)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', self::GOOGLE_PlACES_PATH));
        }

        $date->modify('+' . GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_CACHE_DURATION) . 'minutes');
        GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_CACHE_FILE_TIMESTAMP . strtolower($placeId), $date->getTimestamp());

        file_put_contents(self::GOOGLE_PlACES_FILE . strtolower($placeId) . "_" . $locale .'.json', json_encode($details, JSON_THROW_ON_ERROR));

        return $details;
    }

    /**
     * @param string $placeId
     * @param string $locale
     * @return array|bool|null
     */
    public function getDetailsJson(string $placeId, string $locale): array|bool|null
    {
        try {
            $details = file_get_contents(self::GOOGLE_PlACES_FILE . strtolower($placeId) . "_" . $locale .'.json');

            return json_decode($details, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception|\JsonException $e) {
            return false;
        }
    }

    /**
     * Return details
     * check if details exist and cache duration didn't expire
     *
     * @param string $placeId
     * @param string $locale
     * @return array|null
     * @throws \JsonException
     */
    public function getDetails(string $placeId, string $locale): ?array
    {
        $date = new \DateTime();
        $timestampFile = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_CACHE_FILE_TIMESTAMP . strtolower($placeId));

        if ((!$reviews = $this->getDetailsJson($placeId, $locale)) || $date->getTimestamp() >= $timestampFile) {
            $reviews = $this->saveDetailsJson($placeId, $locale, $date);
        }

        return $reviews;
    }
}
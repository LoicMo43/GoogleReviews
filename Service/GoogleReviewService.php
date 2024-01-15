<?php

namespace GoogleReviews\Service;



use Exception;
use GoogleReviews\GoogleReviews;

class GoogleReviewService
{
    public const GOOGLE_REVIEWS_PATH = THELIA_ROOT . 'public/google_reviews' . DS;
    public const GOOGLE_REVIEWS_FILE = self::GOOGLE_REVIEWS_PATH . 'reviews_';

    public function __construct(protected GoogleApiService $googleApiService)
    {}

    /**
     * Save review in a json file
     * add the cache duration timestamp in module_config
     *
     * @param string $placeId
     * @param string $locale
     * @param \DateTime $date
     * @return array
     * @throws \JsonException
     */
    public function saveReviewJson(string $placeId, string $locale, \DateTime $date): array
    {
        $reviews = $this->googleApiService->getReviews($placeId, $locale);

        if (!file_exists(self::GOOGLE_REVIEWS_PATH)) {
            !mkdir($concurrentDirectory = self::GOOGLE_REVIEWS_PATH) && !is_dir($concurrentDirectory);
        }

        $date->modify('+' . GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_CACHE_DURATION) . 'minutes');
        GoogleReviews::setConfigValue(GoogleReviews::GOOGLE_CACHE_FILE_TIMESTAMP . strtolower($placeId), $date->getTimestamp());

        file_put_contents(self::GOOGLE_REVIEWS_FILE . strtolower($placeId) . "_" . $locale .'.json', json_encode($reviews, JSON_THROW_ON_ERROR));

        return $reviews;
    }

    /**
     * Get the review from json file
     * false file don't exist
     *
     * @param string $placeId
     * @param string $locale
     * @return array|bool
     */
    public function getReviewJson(string $placeId, string $locale): array|bool
    {
        try {
            $reviews = file_get_contents(self::GOOGLE_REVIEWS_FILE . strtolower($placeId) . "_" . $locale .'.json');

            return json_decode($reviews, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception|\JsonException $e) {
            return false;
        }
    }

    /**
     * Return reviews
     * check if reviews exist and cache duration didn't expire
     *
     * @param string $placeId
     * @param string $locale
     * @return array
     * @throws \JsonException
     */
    public function getReviews(string $placeId, string $locale): array
    {
        $date = new \DateTime();
        $timestampFile = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_CACHE_FILE_TIMESTAMP . strtolower($placeId));

        if ((!$reviews = $this->getReviewJson($placeId, $locale)) || $date->getTimestamp() >= $timestampFile) {
            $reviews = $this->saveReviewJson($placeId, $locale, $date);
        }

        return $reviews;
    }
}
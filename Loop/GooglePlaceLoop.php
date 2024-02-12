<?php

namespace GoogleReviews\Loop;

use GoogleReviews\GoogleReviews;
use GoogleReviews\Service\GooglePlaceService;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class GooglePlaceLoop extends BaseLoop implements ArraySearchLoopInterface
{
    public function __construct(protected GooglePlaceService $googlePlaceService)
    {}

    public function parseResults(LoopResult $loopResult): LoopResult
    {
        foreach ($loopResult->getResultDataCollection() as $item) {
            $loopResultRow = new LoopResultRow();

            $loopResultRow->set("ADDRESS", $item['formatted_address'] ?? "");
            $loopResultRow->set("PHONE_NUMBER", $item['formatted_phone_number'] ?? "");
            $loopResultRow->set("LATITUDE", $item['geometry']['location']['lat'] ?? "");
            $loopResultRow->set("LONGITUDE", $item['geometry']['location']['lng'] ?? "");

            $loopResultRow->set("NAME", $item['name'] ?? "");
            $loopResultRow->set("RATING", $item['rating'] ?? 0);
            $loopResultRow->set("RATING_TOTAL", $item['user_ratings_total'] ?? 0);
            $loopResultRow->set("URL", $item['url'] ?? "");
            $loopResultRow->set("VICINITY", $item['vicinity'] ?? "");
            $loopResultRow->set("WEBSITE", $item['website'] ?? "");

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    public function buildArray(): array
    {
        $items = [];

        $locale = $this->getLocale();

        if (null === $placeId = $this->getPlaceId()) {
            $placeId = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_PLACE_ID);
        }

        $items[] = $this->googlePlaceService->getDetails($placeId, $locale);

        return $items;
    }

    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createAlphaNumStringTypeArgument('locale', 'fr_FR'),
            Argument::createAlphaNumStringTypeArgument('place_id')
        );
    }
}
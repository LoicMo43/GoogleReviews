<?php

namespace GoogleReviews\Loop;

use GoogleReviews\GoogleReviews;
use GoogleReviews\Service\GoogleReviewService;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Base\LangQuery;

/**
 * @method getMinScore()
 * @method getCountReviews()
 * @method getLocale()
 * @method getPlaceId()
 */
class GoogleReviewsLoop extends BaseLoop implements ArraySearchLoopInterface
{
    public function __construct(protected GoogleReviewService $googleReviewService)
    {}

    public function parseResults(LoopResult $loopResult): LoopResult
    {
        foreach ($loopResult->getResultDataCollection() as $item) {
            $loopResultRow = new LoopResultRow();

            $loopResultRow->set("REVIEWER_NAME", $item['reviewer_name']);
            $loopResultRow->set("REVIEW", $item['review']);
            $loopResultRow->set("SCORE", $item['score']);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    public function buildArray(): array
    {
        $items = [];

        $minScore = $this->getMinScore();
        $countReviews = $this->getCountReviews();
        $locale = $this->getLocale();

        if (null === $placeId = $this->getPlaceId()) {
            $placeId = GoogleReviews::getConfigValue(GoogleReviews::GOOGLE_PLACE_ID);
        }

        $reviews = $this->googleReviewService->getReviews($placeId, $locale);

        $i = 0;
        foreach ($reviews as $review) {
            if ($i >= $countReviews) {
                break;
            }

            if ($review['rating'] >= $minScore) {
                $items[] = [
                    'reviewer_name' => $review['author_name'],
                    'review' => $review['text'],
                    'score' => $review['rating']
                ];
            }
        }

        return $items;
    }

    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('min_score', 0),
            Argument::createIntTypeArgument('count_reviews', 5),
            Argument::createAlphaNumStringTypeArgument('locale', 'fr_FR'),
            Argument::createAlphaNumStringTypeArgument('place_id')
        );
    }
}
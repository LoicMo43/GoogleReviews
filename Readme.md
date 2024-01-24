# Google Reviews

This module allows you to get your Google reviews on your Thelia website using Google Maps API

## Installation

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/google-reviews-module:~1.0
```

## Usage

 - Configure your Thelia backOffice with your placeId and your API Token
 - Add the [google_reviews_loop] loop in your front template

## Loop

[google_reviews_loop]

### Input arguments

| Argument          | Description                        |
|-------------------|------------------------------------|
| **min_score**     | minimum score allowed. (default 0) |
| **count_reviews** | maximum review allowed.(default 5) |
| **locale**        | locale code.(default 'fr_FR')      |
| **place_id**      | override place_id parameter        |

### Output arguments

| Variable       | Description   |
|----------------|---------------|
| $REVIEWER_NAME | author name   |
| $REVIEW        | message       |
| $SCORE         | minimum score |


[google_places_loop]

### Input arguments

| Argument          | Description                        |
|-------------------|------------------------------------|
| **locale**        | locale code.(default 'fr_FR')      |
| **place_id**      | override place_id parameter        |

### Output arguments

| Variable      | Description               |
|---------------|---------------------------|
| $ADDRESS      | address of the store      |
| $PHONE_NUMBER | phone number of the store |
| $LATITUDE     | latitude of the store     |
| $LONGITUDE    | longitude of the store    |
| $RATING       | rating                    |
| $RATING_TOTAL | number of rating          |
| $URL          | url of the store          |
| $VICINITY     | vicinity                  |
| $WEBSITE      | website of the store      |


## Command

To find your Google place ID easily with geographical coordinates and keyword
```
php Thelia module:GoogleReviews:getPlaceId lat lng keyword
```

## Documentations

Google Maps API documentation is available at https://developers.google.com/maps/documentation/places/web-service?hl=fr

API PLACE Details : https://developers.google.com/maps/documentation/places/web-service/details?hl=fr
API PLACE Find Place : https://developers.google.com/maps/documentation/places/web-service/search-find-place?hl=fr
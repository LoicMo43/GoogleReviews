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

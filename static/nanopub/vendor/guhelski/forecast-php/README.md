forecast-php
============

Drop dead simple [Forecast.io](http://forecast.io) API v2 wrapper in PHP.

This lets you get from the [Forecast.io API docs](https://developer.forecast.io/docs/v2) to the code as directly as possible.

PSR friendly and requires nothing. Abstractions not included.

Installation
------------

Easy breezy using [Composer](http://getcomposer.org):
```json
{
    "require": {
        "guhelski/forecast-php": "1.1.*"
    }
}
```

Usage
-----

As simple as calling one method.
```php
<?php

use Forecast\Forecast;

$forecast = new Forecast('YOUR_API_KEY');
  
// Get the current forecast for a given latitude and longitude
var_dump($forecast->get('37.8267','-122.423'));
  
// Get the forecast at a given time
var_dump($forecast->get('37.8267','-122.423', '2013-05-06T12:00:00-0400'));
  
// Use some optional query parameters
var_dump($forecast->get(
    '37.8267',
    '-122.423',
    null,
    array(
        'units' => 'si',
        'exclude' => 'flags'
        )
    )
);
 ```
 
 For more details and all available options check the [official documentation](https://developer.forecast.io/docs/v2).

Timezone
========

This library provides a function for retrieving the timezone for a given location.

It is implemented as a single file with no external dependencies. To do this, a horrible cheat is used. The timezone for a location is found by looking up the timezone of the nearest city. This means it's possible that the wrong timezone will be returned for locations near the borders between timezones, so you should only use this library if that is an acceptable compromise. The tradeoff is no fancy geometry or databases are required so this is comparatively fast.

API
---

```php
$timezone = p3k\Timezone::timezone_for_location($latitude, $longitude);
```

Returns a string with the timezone name such as "Europe/Berlin", which can be used to create a new `DateTimeZone` object.


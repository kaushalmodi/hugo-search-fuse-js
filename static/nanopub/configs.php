<?php

return (object) array(
    // First some settings for the site.
    'siteUrl' => 'https://scripter.co/',                // the URL for your site - note trailing slash
    'timezone' => 'America/New_York',                   // http://php.net/manual/en/timezones.php
    // -- Thu Apr 12 15:19:52 EDT 2018 - kmodi
    // What is the mediaPoint?
    'mediaPoint' => 'https://media.org/endpoint',       // Micropub Media Endpoint

    // // Config Block for Twitter
    // -- Thu Apr 12 15:21:58 EDT 2018 - kmodi
    // Not sure if the twitter api stuff can be committed in public. Most likely not.
    // 'twitterName' => 'kaushalmodi',                  // your twitter account name, don't use the @
    // 'twAPIkey' => 'WomtvR2YoT',                      // Create an app on dev.twitter.com for your account.
    // 'twAPIsecret' => 'NILIDJXg1e',                   // APIkey & APIsecret are the APP's key & Secret
    // 'twUserKey' => 'ILs4jUS7a6',                     // UserKey & User Secret are under 'Your access token'
    // 'twUserSecret' => 'NYbGUfuNUh',                  // Generate those on dev.twitter.com

    // Config Block for Mastodon
    // -- Thu Apr 12 15:23:36 EDT 2018 - kmodi
    // Not sure if the mastodon api stuff can be committed in public. Most likely not.
    // Also, where do I get my mastodon token from?
    // 'mastodonInstance' => 'servername.ext',          // your Mastodon Instance
    // 'mastodonToken' => 'uWo42Bca91',                 // get an auth code using Mastodon docs

    // Config for micro.blog
    'pingMicro' => True,                                // Set to False (boolean) if you don't use micro.blog
    'siteFeed' => 'https://scripter.co/atom.xml',       // Set to your site's RSS/Atom Feed to notify micro.blog

    // Config for Weather. If you do want weather feature, set to true.
    'weatherToggle' => false,
    'compass' => 'https://private.tracker.com/api',
    'compassKey' => 'PrivateAPIkey',
    'forecastKey' => 'DarkSkyApiKey',
    'defaultLat' => '51.5074',
    'defaultLong' => '0.1278',
    'defaultLoc' => 'London',

    // Set Frontmatter Format -- json or yaml
    'frontFormat' => 'json'
);

?>

<?php

use GuzzleHttp\Client;
use Forecast\Forecast;

/**
 * Uses Compass and DarkSky to obtain weather values
 *
 * @return (array) $weather Weather data from resource
 */

function getWeather()
{
    $configs = include 'configs.php';
    $client = new Client(
        [
            'base_uri' => $configs->compass,
            'query' => [
                    'token' => $configs->compassKey,
                    'geocode' => true
            ]
        ]
    );

    $response = $client->request('GET', 'last');
    $body = json_decode($response->getBody(), true);
    $lat = $body['geocode']['latitude'] ?? $configs->defaultLat;
    $long = $body['geocode']['longitude'] ?? $configs->defaultLong;
    $loc = $body['geocode']['best_name'] ?? $configs->defaultLoc;

    $forecast = new Forecast($configs->forecastKey);
    $weather = $forecast->get(
        $lat,
        $long,
        null,
        array(
            'units' => 'si',
            'exclude' => 'minutely,hourly,daily,alert,flags'
        )
    );

    $response = [];
    $response['loc'] = $loc;
    $response['weather'] = $weather->currently->summary;
    $response['wicon'] = $weather->currently->icon;
    $response['temp'] = (string) round($weather->currently->temperature, 1);
    return $response;
}

/**
 * @since 1.4
 * Tries to obtain metadata from a given url
 *
 * @param $url    The uri of the resource to be parsed
 *
 * @return $resp Array of parsed data from resource
 */


function tagRead($url) 
{
    date_default_timezone_set($configs->timezone);
    $cdate = date('c', time());

    $tags = array();
    $site_html=  file_get_contents($url);
    $site_html = str_replace('data-react-helmet="true"', '', $site_html);
    preg_match_all('/<head>(.*?)<\/head>/si', $site_html, $head);
    $data = $head['0']['0'];

    if ($data !== false ) {
        preg_match_all(
            '/<[\s]*meta[\s]*(name|property)="?' . '([^>"]*)"?[\s]*'
                     . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', 
            $data, $match
        );

        $count = count($match['3']);
        if ($count != 0) {
            $i = 0; do {

                $key = $match['2']["$i"];
                $key = trim($key);
                $value = $match['3']["$i"];
                $value = trim($value);

                $tags["$key"] = "$value"; $i++;
            } while ($i < $count);
        }

    }

    $resp['xAuthor'] = $tags['author'] ?? $tags['article:author'] ?? 
                    $tags['parsely-author'] ?? $tags['twitter:creator'] ?? 
                    $tags['og:site_name'] ?? hostname_of_uri($url);

    $resp['xContent'] = $tags['title'] ?? $tags['og:title'] ?? 
                    $tags['twitter:title'] ?? $tags['parsely-title'] ?? 
                    $tags['sailthru.title'] ?? 'An Article';

    $resp['xSummary'] = $tags['description'] ?? $tags['og:description'] ?? 
                    $tags['twitter:description'] ?? 
                    $tags['sailthru.description'] ?? 
                    'About something interesting';

    $strDate = $tags['article:published_time'] ?? $tags['datePublished'] ?? 
            $tags['date'] ?? $tags['pubdate'] ?? $tags['sailthru.date'] ?? 
            $tags['parsely-pub-date'] ?? $tags['DC.date.issued'] ?? $cdate;

    $resp['xPublished'] = date("c", strtotime($strDate));

    $resp['site'] = $tags['og:site_name'] ?? $tags['twitter:site'];


    return $resp;
}

/**
 * @since 1.2
 * Uses the XRay library to extract rich content from uris
 *
 * @param $url    The uri of the resource to be parsed
 * @param $site   The hostname of the resource to be parsed
 *                Could specify other services in configs.php
 * @return $url_parse Array of parsed data from resource
 */

function xray_machine($url, $site)
{
    $xray = new p3k\XRay();
    if ($site == "twitter.com") {
        $configs = include 'configs.php';
        $twAPIkey = $configs->twAPIkey;
        $twAPIsecret = $configs->twAPIsecret;
        $twUserKey = $configs->twUserKey;
        $twUserSecret = $configs->twUserSecret;
        $url_parse = $xray->parse(
            $url, 
            [
              'timeout' => 30,
              'twitter_api_key' => $twAPIkey,
              'twitter_api_secret' => $twAPIsecret,
              'twitter_access_token' => $twUserKey,
              'twitter_access_token_secret' => $twUserSecret
            ]
        );
    } else {
        $url_parse = $xray->parse($url);
    }
    if (empty($url_parse['data']['published'])) {
        $result = tagRead($url);
    } else {
        $result['xAuthor'] = $url_parse['data']['author']['name'];
        $result['xAuthorUrl'] = $url_parse['data']['author']['url'];
        $result['xPhoto'] = $url_parse['data']['author']['photo'];
        $xContent = $url_parse['data']['content']['text'] ?? null;
        $result['xContent'] = $url_parse['data']['name'] ?? 
                            $url_parse['data']['content']['html'] ?? 
                            auto_link($xContent, false) ?? 'A Post';
        $result['xSummary'] = $url_parse['summary'];
        $result['xPublished'] = $url_parse['data']['published'];
        if (isset($url_parse['data']['category'])) {
            $result['tags'] = $url_parse['data']['category'];
        }
    }
    return $result;
}

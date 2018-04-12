# p3k-http

A simple HTTP client, used by https://p3k.io projects.

## Usage

### GET

```php
$http = new p3k\HTTP();
$headers = [
  'Accept: text/html, */*'
];
$response = $http->get('http://example.com/', $headers);
```

### POST

```php
$http = new p3k\HTTP();
$headers = [
  'Accept: application/json',
  'Content-type: application/json'
];
$response = $http->post('http://example.com/', json_encode([
  'foo' => 'bar'
], $headers);
```

### HEAD

```php
$http = new p3k\HTTP();
$headers = [
  'Accept: text/html, */*'
];
$response = $http->head('http://example.com/', $headers);
```

### Response

The get/post/head functions will return an array with the following properties:

* `code` - integer, the HTTP response code that was returned
* `headers` - array, the HTTP headers returned
* `rels` - array, the parsed HTTP rel values from any `Link` headers
* `body` - string, the body of the HTTP response, or false/omit for a HEAD request
* `error` - string, an error string. see below for the enumerated list.
* `error_description` - string,
* `url` - string, the final URL retrieved after following any redirects
* `debug` - string, the full HTTP response

#### `headers`

The `headers` key will be an array of all the header values returned. The values will be either a string or array depending on whether there were multiple values returned for a given header name.

```
    [headers] => Array
        (
            [Server] => nginx/1.12.0
            [Content-Type] => text/html; charset=UTF-8
            [Transfer-Encoding] => chunked
            [Connection] => keep-alive
            [Cache-Control] => no-cache
            [Link] => Array
                (
                    [0] => <https://switchboard.p3k.io/>; rel="hub"
                    [1] => <https://aaronparecki.com/auth>; rel="authorization_endpoint"
                    [2] => <https://aaronparecki.com/micropub>; rel="micropub"
                    [3] => <https://aaronparecki.com/auth/token>; rel="token_endpoint"
                    [4] => <https://aaronparecki.com/>; rel="self"
                )

            [Date] => Fri, 28 Apr 2017 18:40:42 GMT
            [Strict-Transport-Security] => max-age=2592000
            [X-No-Cache] => 0
            [X-Cache] => EXPIRED
        )
```

#### `rels`

The `rels` key will be the parsed version of any HTTP `Link` headers that contain a rel value. All values will be arrays even if there is only one value.

```
    [rels] => Array
        (
            [hub] => Array
                (
                    [0] => https://switchboard.p3k.io/
                )

            [authorization_endpoint] => Array
                (
                    [0] => https://aaronparecki.com/auth
                )

            [micropub] => Array
                (
                    [0] => https://aaronparecki.com/micropub
                )

            [token_endpoint] => Array
                (
                    [0] => https://aaronparecki.com/auth/token
                )

            [self] => Array
                (
                    [0] => https://aaronparecki.com/
                )
        )
```


### Options

There are a few options you can set when making HTTP requests, to configure how the request will behave.

* `$http->set_user_agent('String')` - A shortcut for setting the user agent header.
* `$http->set_max_redirects(2)` - The maximum number of redirects to follow. Defaults to 8.
* `$http->set_timeout(10)` - The timeout in seconds for waiting for a response. Defaults to 4.

#### User Agent

You can set the user agent when you first instantiate the HTTP object:

```php
$http = new p3k\HTTP('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36 p3k-http/0.1.0');
$http->get('http://example.com/');
```

Alternately, you can change it before each request:

```php
$http = new p3k\HTTP();
$http->set_user_agent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36 p3k-http/0.1.0');
$http->get('http://example.com/');
```


## Transports

By default, the library will use the PHP curl functions to make the HTTP request.

You can optionally define your own transport to use to make HTTP requests instead. This allows you to use an existing HTTP request mechanism you may have rather than using curl or the PHP stream functions.

Define a new class that implements the `p3k\HTTP\Transport` interface. That interface documents the return values expected as well.

Then you can set the transport after you create the main HTTP object.

```php
$http = new p3k\HTTP();
$http->set_transport(new p3k\HTTP\Stream());  // or your custom class here
```

The library ships with two alternative transport mechanisms, `p3k\HTTP\Stream` and `p3k\HTTP\Test`. The Stream transport uses `file_get_contents` with all the necessary config options to make it work. This is useful when running in Google App Engine. The Test transport will read HTTP responses from files, so that you can write tests that simulate making HTTP calls. See https://github.com/aaronpk/XRay/tree/master/tests for an example of using this transport.


## License

Copyright 2017 by Aaron Parecki

Available under the MIT license.


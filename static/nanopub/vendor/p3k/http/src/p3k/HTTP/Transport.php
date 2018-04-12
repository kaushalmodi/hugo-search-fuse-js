<?php
namespace p3k\HTTP;

interface Transport {

  /*
    Return an array with the following keys:
    * code - integer, the HTTP response code that was returned
    * header - string, the HTTP headers returned
    * body - string, the body of the HTTP response, or false/omit for a HEAD request
    * error - string, an error string. see below for the enumerated list.
    * error_description - string,
    * url - string, the final URL retrieved after following any redirects
    * debug - string, the full HTTP response

    Error Strings:
    * dns_error
    * connect_error
    * timeout
    * ssl_error
    * ssl_cert_error
    * ssl_unsupported_cipher
    * too_many_redirects
    * unknown
  */

  public function get($url, $headers=[]);
  public function post($url, $body, $headers=[]);
  public function head($url, $headers=[]);

  public function set_timeout($timeout);
  public function set_max_redirects($max_redirects);

}

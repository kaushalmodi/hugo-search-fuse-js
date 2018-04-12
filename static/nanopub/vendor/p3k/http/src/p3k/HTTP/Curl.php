<?php
namespace p3k\HTTP;

class Curl implements Transport {

  protected $_timeout = 4;
  protected $_max_redirects = 8;

  public function set_max_redirects($max) {
    $this->_max_redirects = $max;
  }

  public function set_timeout($timeout) {
    $this->_timeout = $timeout;
  }

  public function get($url, $headers=[]) {
    $ch = curl_init($url);
    $this->_set_curlopts($ch, $url);
    if($headers)
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header_str = trim(substr($response, 0, $header_size));
    return [
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'header' => $header_str,
      'body' => substr($response, $header_size),
      'error' => self::error_string_from_code(curl_errno($ch)),
      'error_description' => curl_error($ch),
      'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
      'debug' => $response
    ];
  }

  public function post($url, $body, $headers=[]) {
    $ch = curl_init($url);
    $this->_set_curlopts($ch, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    if($headers)
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header_str = trim(substr($response, 0, $header_size));
    return [
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'header' => $header_str,
      'body' => substr($response, $header_size),
      'error' => self::error_string_from_code(curl_errno($ch)),
      'error_description' => curl_error($ch),
      'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
      'debug' => $response
    ];
  }

  public function head($url, $headers=[]) {
    $ch = curl_init($url);
    $this->_set_curlopts($ch, $url);
    if($headers)
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    $response = curl_exec($ch);
    return [
      'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
      'header' => trim($response),
      'error' => self::error_string_from_code(curl_errno($ch)),
      'error_description' => curl_error($ch),
      'url' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
      'debug' => $response
    ];
  }

  private function _set_curlopts($ch, $url) {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if($this->_max_redirects > 0)
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $this->_max_redirects);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, round($this->_timeout * 1000));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000);
  }

  public static function error_string_from_code($code) {
    switch($code) {
      case 0:
        return '';
      case CURLE_COULDNT_RESOLVE_HOST:
        return 'dns_error';
      case CURLE_COULDNT_CONNECT:
        return 'connect_error';
      case CURLE_OPERATION_TIMEDOUT:
        return 'timeout';
      case CURLE_SSL_CONNECT_ERROR:
        return 'ssl_error';
      case CURLE_SSL_CERTPROBLEM:
        return 'ssl_cert_error';
      case CURLE_SSL_CIPHER:
        return 'ssl_unsupported_cipher';
      case CURLE_SSL_CACERT:
        return 'ssl_cert_error';
      case CURLE_TOO_MANY_REDIRECTS:
        return 'too_many_redirects';
      default:
        return 'unknown';
    }
  }
}

<?php
namespace p3k\HTTP;

class Stream implements Transport {

  private $_timeout = 4;
  private $_max_redirects = 8;

  public function set_max_redirects($max) {
    $this->_max_redirects = $max;
  }

  public function set_timeout($timeout) {
    $this->_timeout = $timeout;
  }

  public static function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
      // This error code is not included in error_reporting
      return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
  }

  public function get($url, $headers=[]) {
    set_error_handler("p3k\HTTP\Stream::exception_error_handler");
    $context = $this->_stream_context('GET', $url, false, $headers);
    return $this->_fetch($url, $context);
  }

  public function post($url, $body, $headers=[]) {
    set_error_handler("p3k\HTTP\Stream::exception_error_handler");
    $context = $this->_stream_context('POST', $url, $body, $headers);
    return $this->_fetch($url, $context);
  }

  public function head($url, $headers=[]) {
    set_error_handler("p3k\HTTP\Stream::exception_error_handler");
    $context = $this->_stream_context('HEAD', $url, false, $headers);
    return $this->_fetch($url, $context);
  }

  private function _fetch($url, $context) {
    $error = false;

    try {
      $body = file_get_contents($url, false, $context);
      // This sets $http_response_header
      // see http://php.net/manual/en/reserved.variables.httpresponseheader.php
    } catch(\Exception $e) {
      $body = false;
      $http_response_header = [];
      $description = str_replace('file_get_contents(): ', '', $e->getMessage());
      $code = 'unknown';

      if(preg_match('/getaddrinfo failed/', $description)) {
        $code = 'dns_error';
        $description = str_replace('php_network_getaddresses: ', '', $description);
      }

      if(preg_match('/timed out|request failed/', $description)) {
        $code = 'timeout';
      }

      if(preg_match('/certificate/', $description)) {
        $code = 'ssl_error';
      }

      $error = [
        'description' => $description,
        'code' => $code
      ];
    }

    return [
      'code' => self::parse_response_code($http_response_header),
      'header' => implode("\r\n", $http_response_header),
      'body' => $body,
      'error' => $error ? $error['code'] : false,
      'error_description' => $error ? $error['description'] : false,
      'url' => $url,
    ];
  }

  private function _stream_context($method, $url, $body=false, $headers=[]) {
    $options = [
      'method' => $method,
      'timeout' => $this->_timeout,
      'ignore_errors' => true,
    ];

    if($body) {
      $options['content'] = $body;
    }

    if($headers) {
      $options['header'] = implode("\r\n", $headers);
    }

    if($this->_max_redirects > 0) {
      $options['follow_location'] = 1;
      $options['max_redirects'] = $this->_max_redirects;
    } else {
      $options['follow_location'] = 0;
    }

    return stream_context_create(['http' => $options]);
  }

  public static function parse_response_code($headers) {
    // When a response is a redirect, we want to find the last occurrence of the HTTP code
    $code = false;
    foreach($headers as $field) {
      if(preg_match('/HTTP\/\d\.\d (\d+)/', $field, $match)) {
        $code = $match[1];
      }
    }    
    return (int)$code;
  }

}

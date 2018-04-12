<?php
namespace p3k\HTTP;

// This implements the same interface as the main class p3k\HTTP
// despite the fact that it looks like just a transport plugin.
// This is so that the main p3k\HTTP object can be replaced with this
// object in the test suite.

class Test implements Transport {

  private $_testDataPath;
  private $_redirects_remaining;

  public function __construct($testDataPath) {
    $this->_testDataPath = $testDataPath;
  }

  protected $_timeout = 4;
  protected $_max_redirects = 8;

  public function set_max_redirects($max) {
    $this->_max_redirects = $max;
  }

  public function set_timeout($timeout) {
    $this->_timeout = $timeout;
  }

  public function set_transport(Transport $transport) {
  }

  public function get($url, $headers=[]) {
    $this->_redirects_remaining = $this->_max_redirects;
    $parts = parse_url($url);
    unset($parts['fragment']);
    $url = self::_build_url($parts);
    return $this->_read_file($url);
  }

  public function post($url, $body, $headers=[]) {
    return $this->_read_file($url);
  }

  public function head($url, $headers=[]) {
    $response = $this->_read_file($url);
    return [
      'code' => (int)$response['code'],
      'headers' => $response['headers'],
      'rels' => $response['rels'],
      'error' => '',
      'error_description' => '',
      'url' => $response['url']
    ];
  }

  private function _read_file($url) {
    $parts = parse_url($url);
    if($parts['path']) {
      $parts['path'] = '/'.str_replace('/','_',substr($parts['path'],1));
      $filepathurl = self::_build_url($parts);
    }

    $filename = $this->_testDataPath.preg_replace('/https?:\/\//', '', $filepathurl);
    if(!file_exists($filename)) {
      $filename = $this->_testDataPath.'404.response.txt';
    }
    $response = file_get_contents($filename);

    $split = explode("\r\n\r\n", $response);
    if(count($split) < 2) {
      throw new \Exception("Invalid file contents in test data, check that newlines are CRLF: $url");
    }
    $headers = array_shift($split);
    $body = implode("\r\n", $split);

    if(preg_match('/HTTP\/1\.1 (\d+)/', $headers, $match)) {
      $code = $match[1];
    }

    $headers = preg_replace('/HTTP\/1\.1 \d+ .+/', '', $headers);
    $parsedHeaders = self::_parse_headers($headers);

    if(array_key_exists('Location', $parsedHeaders)) {
      $effectiveUrl = \mf2\resolveUrl($url, $parsedHeaders['Location']);
      if($this->_redirects_remaining > 0) {
        $this->_redirects_remaining--;
        return $this->_read_file($effectiveUrl);
      } else {
        return [
          'code' => 0,
          'headers' => $parsedHeaders,
          'rels' => \IndieWeb\http_rels($headers),
          'body' => $body,
          'error' => 'too_many_redirects',
          'error_description' => '',
          'url' => $effectiveUrl
        ];
      }
    } else {
      $effectiveUrl = $url;
    }

    return [
      'code' => (int)$code,
      'headers' => $parsedHeaders,
      'rels' => \IndieWeb\http_rels($headers),
      'body' => $body,
      'error' => (isset($parsedHeaders['X-Test-Error']) ? $parsedHeaders['X-Test-Error'] : ''),
      'error_description' => '',
      'url' => $effectiveUrl
    ];
  }

  private static function _parse_headers($headers) {
    $retVal = [];
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach($fields as $field) {
      if(preg_match('/([^:]+): (.+)/m', $field, $match)) {
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        // If there's already a value set for the header name being returned, turn it into an array and add the new value
        $match[1] = preg_replace_callback('/(?<=^|[\x09\x20\x2D])./', function($m) {
          return strtoupper($m[0]);
        }, strtolower(trim($match[1])));
        if(isset($retVal[$match[1]])) {
          if(!is_array($retVal[$match[1]]))
            $retVal[$match[1]] = [$retVal[$match[1]]];
          $retVal[$match[1]][] = $match[2];
        } else {
          $retVal[$match[1]] = trim($match[2]);
        }
      }
    }
    return $retVal;
  }

  private static function _build_url($parsed_url) {
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
  }

}

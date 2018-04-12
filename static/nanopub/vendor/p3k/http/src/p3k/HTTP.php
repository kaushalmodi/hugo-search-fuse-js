<?php
namespace p3k;

class HTTP {

  public $_timeout = 4;
  public $_max_redirects = 8;

  private $_transport;
  private $_user_agent;

  public function __construct($user_agent=null, HTTP\Transport $transport=null) {
    if($user_agent) {
      $this->_user_agent = $user_agent;
    }
    if(!$transport) {
      $this->_transport = new HTTP\Curl();
    } else {
      $this->set_transport($transport);
    }
  }

  public function set_user_agent($ua) {
    $this->_user_agent = $ua;
  }

  public function set_max_redirects($max) {
    $this->_max_redirects = $max;
  }

  public function set_timeout($timeout) {
    $this->_timeout = $timeout;
  }

  public function set_transport(HTTP\Transport $transport) {
    $this->_transport = $transport;
  }

  public function get($url, $headers=[]) {
    $this->_transport->set_timeout($this->_timeout);
    $this->_transport->set_max_redirects($this->_max_redirects);
    if($this->_user_agent) {
      $headers[] = 'User-Agent: ' . $this->_user_agent;
    }
    $response = $this->_transport->get($url, $headers);
    $response = $this->_build_response($response);
    return $response;
  }

  public function post($url, $body, $headers=[]) {
    $this->_transport->set_timeout($this->_timeout);
    $this->_transport->set_max_redirects($this->_max_redirects);
    if($this->_user_agent) {
      $headers[] = 'User-Agent: ' . $this->_user_agent;
    }
    $response = $this->_transport->post($url, $body, $headers);
    $response = $this->_build_response($response);
    return $response;
  }

  public function head($url, $headers=[]) {
    $this->_transport->set_timeout($this->_timeout);
    $this->_transport->set_max_redirects($this->_max_redirects);
    if($this->_user_agent) {
      $headers[] = 'User-Agent: ' . $this->_user_agent;
    }
    $response = $this->_transport->head($url, $headers);
    $response = $this->_build_response($response);
    return $response;
  }

  private function _build_response($response) {
    // Parses the HTTP headers and adds the "headers" and "rels" response keys
    $response['headers'] = self::_parse_headers($response['header']);
    $response['rels'] = \IndieWeb\http_rels($response['header']);
    unset($response['header']);
    return $response;
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
}

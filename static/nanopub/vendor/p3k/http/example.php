<?php
require('vendor/autoload.php');

$http = new p3k\HTTP();
$http->set_user_agent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36 p3k-http/0.1.0');
$headers = [
  'Accept: text/html'
];
$response = $http->get('http://aaronpk.com', $headers);

print_r($response['headers']);


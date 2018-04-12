<?php
/* sample functions for using the link rel parser library to get first webmention (and pingback) endpoints */

/* 
first_linknode_href, get_rel_webmention by Tantek Çelik http://tantek.com/
license: http://creativecommons.org/publicdomain/zero/1.0/
depends on: link_rel_parser.php (e.g. head_http_rels)
depends on: https://github.com/tantek/cassis/cassis.js (e.g. contains, get_absolute_uri, is_html_type, xphasrel, strcat)
*/

// Could move this function to cassis.js if more broadly useful
function is_loopback($href) {
// in: $href URL
// out: boolean whether host of URL is a loopback address
  $host = hostname_of_uri($href);
  if ($host == 'localhost') { return true; }
  $host = explode('.', $host);
  return ($host.length == 4 &&
          $host[0] == 127 &&
          ctype_digit($host[1]) &&
          ctype_digit($host[2]) &&
          ctype_digit($host[3]));
}

function first_linknode_href($links, $spacedtagnames='a area link') {
// in: DOMNodeList $links
//     $spacedtagnames - space separated tag names, null for any
// out: href attribute as string
// return href of first DOMNode in $links that is an a, area, link
//      with href that is not a loopback address
// else return null
  if ($spacedtagnames) {
    $spacedtagnames = strcat(' ', $spacedtagnames, ' ');
  }
  foreach ($links as $link) {
    if (!$spacedtagnames ||
        contains($spacedtagnames, 
                 strcat(' ', $link->nodeName, ' '))) {
      $href = $link->getAttribute('href');
      if (!is_loopback($href))
      {
        return $href;
      }
    }
  }
  return null;
}

// in: $url of page that may or may not have a webmention endpoint
// out: array of 'webmention' URL of webmention endpoint if any, 
//           and 'pingback' URL of pingback endpoint if any
function get_rel_webmention($url) {
  global $debug;
  $r = array();
  $r['webmention'] = '';
  $r['pingback'] = '';
  
  $httprels = head_http_rels($url);
  if ($debug) {
    echo 'head_http_rels STATUS:"'.$httprels['status'].'"<br/>';
  }
  if ($httprels['status'] != "200") {
    return $r;
  }
  
  if ($debug) {
    echo 'HEAD Content-Type: '.$httprels['type'].' '.
         string(is_html_type($httprels['type'])).'<br/>';
  }
  $wm = '';
  $pb = '';
  if (array_key_exists('webmention', $httprels['rels'])) {
    $wm = $httprels['rels']['webmention'][0];
    // just use the first one.
  }
  if (array_key_exists('pingback', $httprels['rels'])) {
    $pb = $httprels['rels']['pingback'][0];
    // just use the first one.
  }
  if ($debug && $wm) {
    echo "HEAD LINK webmention: '$wm'<br/>";
  }
  if ($debug && $pb) {
    echo "HEAD LINK pingback: '$pb'<br/>";
  }
  if (!$wm && is_html_type($httprels['type'])) {
    // no webmention endpoint in HTTP headers, check HTML
    if ($debug) {
      echo "looking for wm endpoint in HTML $url<br/>";
    }
    $ch = curl_init($url);
//  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// commented out due to:
// Warning: curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $s = curl_exec($ch);
    curl_close($ch);
    if ($s != '') {
      $dom = new DOMDocument();
      $dom->loadHTML($s); // ** maybe only load part of it?
      $domx = new DOMXPath($dom);
      $wms = $domx->query(xphasrel('webmention'));
      if ($wms) { $wms = first_linknode_href($wms); }
      if ($debug) {
        echo "query xphasrel webmention $wms<br/>";
      }

      if ($wms !== null) {
        $wm = get_absolute_uri($wms, $url);
      }
      if ($debug && $wm) {
        echo "HTML rel=webmention returned '$wm'<br/>";
      }
      $wms = $domx->query(xphasrel('pingback'));
      if ($wms) { $wms = first_linknode_href($wms, 'link'); }
      if ($debug) {
        echo "query xphasrel pingback $wms<br/>";
      }

      if ($wms !== null) {
        $pb = get_absolute_uri($wms, $url);
      }
      if ($debug && $pb) {
        echo "HTML rel=pingback returned '$pb'<br/>";
      }
    }
  }
  $r['webmention'] = $wm;
  $r['pingback'] = $pb;
  return $r;
}

?>

<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/utils_url.php,v 1.1 2008-01-15 09:21:14 mose Exp $

  function guess_url($path, $baseurl) {
    // Check if path is absolute
    // 'Path' is starting with protocol identifier?
    if (preg_match("!^[a-zA-Z]+://.*!",$path)) {
      return $path;
    };
    // 'Path' is starting at root?
    if (substr($path,0,1) == "/") {
      return 'http://' . get_host($baseurl) . $path;
    };
    // 'Path' is relative from the vurrent position
    return 'http://' . get_path($baseurl) . $path;
  };

  function get_host($baseurl) {
    if (preg_match("!^[a-zA-Z]+://([^/]+)/.*!",$baseurl,$matches)) {
      return $matches[1];
    };
    if (preg_match("!^[a-zA-Z]+://([^/]+)$!",$baseurl,$matches)) {
      return $matches[1];
    };
    preg_match("!^([^/]+)(/.*)?!",$baseurl);
    return $matches[1];
  };

  function get_path($baseurl) {
    if (preg_match("!^[a-zA-Z]+://(.*)/[^/]*$!",$baseurl,$matches)) {
      return $matches[1] . "/";
    };
    if (preg_match("!^[a-zA-Z]+://(.*)$!",$baseurl,$matches)) {
      return $matches[1] . "/";
    };
    preg_match("!^(.*)/[^/]*$!",$baseurl,$matches);
    return $matches[1] . "/";    
  };
?>
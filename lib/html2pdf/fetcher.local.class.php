<?php

class FetcherLocalFile {
  var $protocol;
  var $host;
  var $port;
  var $path;

  var $headers;
  var $content;
  
  var $redirects;

  // ---------------------------------------------
  // FetcherLocalFile - PUBLIC methods 
  // ---------------------------------------------  

  // Constructor

  function Fetcher($restrict_path) {
    $this->restrict_path = $restrict_path;
    $this->error_message = "";
  }

  // "Fetcher" interface implementation

  function get_base_url() {
    return $this->path;
  }

  function get_data($path) {
    $this->path    = $path;
    $this->content = file_get_contents($url);
    return true;
  }

  function error_message() {
    return $this->error_message;
  }
}
?>
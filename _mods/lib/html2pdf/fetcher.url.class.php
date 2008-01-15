<?php

define('HTTP_OK',200);

class FetcherUrl {
  var $protocol;
  var $host;
  var $port;
  var $path;

  var $url;

  var $headers;
  var $content;
  var $code;

  var $redirects;

  // ---------------------------------------------
  // FetcherURL - PUBLIC methods
  // ---------------------------------------------

  // "Fetcher" interface implementation

  function get_base_url() {
    return $this->url;
  }

  function get_data($data_id) {
    if ($this->fetch($data_id)) {
      if ($this->code != HTTP_OK) {
        $_server_response = $this->headers;
        $_http_error = $this->code;
        $_url = htmlspecialchars($data_id);

        ob_start();
        include('templates/error._http.tpl');
        $this->error_message = ob_get_contents();
        ob_end_clean();

        error_log("Cannot open $data_id, HTTP result code is: ".$this->code);

        return null;
      };

      return new FetchedDataURL($this->content,
                                explode("\r\n",$this->headers),
                                $this->url);
    } else {
      die("Cannot open $data_id");
      return null;
    }
  }

  function error_message() {
    return $this->error_message;
  }

  // FetcherURL - constructor

  function FetcherURL() {
    $this->error_message = "";

    $this->redirects = 0;
    $this->port = 80;

    // Default encoding
    //    $this->encoding = "iso-8859-1";

    $this->user_agent = DEFAULT_USER_AGENT;
  }

  // ---------------------------------------------
  // FetcherURL - PRIVATE methods
  // ---------------------------------------------

  /**
   * Connects to the target host using either HTTP or HTTPS protocol;
   * returns handle to connection socked or 'null' in case connection failed.
   *
   * @access private
   * @final
   * @return resource
   */
  function _connect() {
    // Connect to the targt host
    if ($this->protocol == "https") {
       $fp = @fsockopen("ssl://$this->host", $this->port, $errno, $errstr, 5);
    } else {
       $fp = @fsockopen($this->host,$this->port,$errno,$errstr,5);
    }

    if (!$fp) {
      error_log("Cannot connect to ".$this->host.":".$this->port." - (".$errno.")".$errstr);
      return null;
    };

    return $fp;
  }

  function _extract_code($res) {
    // Check return code
    // Note the return code will always be contained in the response, so
    // the we may not check the result of 'preg_match' - it matches always.
    //
    // A month later: nope, not always.
    //
    if (preg_match('/\s(\d+)\s/',$res,$matches)) {
      $result = $matches[1];
    } else {
      $result = "200";
    };

    return $result;
  }

  function _fix_location($location) {
    if (substr($location, 0, 7) == "http://") { return $location; };
    if (substr($location, 0, 8) == "https://") { return $location; };

    if ($location{0} == "/") {
      return $this->protocol."://".$this->host.$location;
    };

    return $this->protocol."://".$this->host.$this->path.$location;
  }

  function fetch($url) {
    error_log("Fetching: $url");

    $this->url = $url;

    $parts = parse_url($this->url);

    if (isset($parts['scheme']))   { $this->protocol  = $parts['scheme'];    };
    if (isset($parts['host']))     { $this->host      = $parts['host'];      };
    if (isset($parts['port']))     { $this->port      = $parts['port'];      };
    if (isset($parts['path']))     { $this->path      = $parts['path'];      } else { $this->path = "/"; };
    if (isset($parts['query']))    { $this->path     .= '?'.$parts['query']; };

    if ($this->protocol <> 'http' && $this->protocol <> 'https') {
      $this->error_message = "Unsupported protocol: ".$this->protocol;
      return null;
    };

    if ($this->protocol == "https" && !isset($parts['port'])) {
       $this->port = 443;
    }

    $res = $this->_head();

    if (is_null($res)) { return null; };
    $this->code = $this->_extract_code($res);

    return $this->_process_code($res);
  }

  function _get() {
    $socket = $this->_connect();
    if (is_null($socket)) { return null; };

    // Build the HEAD request header (we're saying we're just a browser as some pages don't like non-standard user-agents)
    $header  = "GET ".$this->path." HTTP/1.1\r\n";
    $header .= "Host:".$this->host."\r\n";
    $header .= "User-Agent: ".$this->user_agent."\r\n";
    $header .= "Connection: close\r\n";
    $header .= "Referer: ".$this->protocol."://".$this->host.$this->path."\r\n\r\n";
    // Send the header
    fputs ($socket, $header);
    // Get the responce
    $res = "";

    // The PHP-recommended construction
    //    while (!feof($fp)) { $res .= fread($fp, 4096); };
    // hangs indefinitely on www.searchscout.com, for example.
    // seems that they do not close conection on their side or somewhat similar;

    // let's assume that there will be no HTML pages greater than 1 Mb

    $res = fread($socket, 1024*1024);

    // Close connection handle, we do not need it anymore
    fclose($socket);

    return $res;
  }

  function _head() {
    $socket = $this->_connect();
    if (is_null($socket)) { return null; };

    // Build the HEAD request header (we're saying we're just a browser as some pages don't like non-standard user-agents)
    $header  = "HEAD ".$this->path." HTTP/1.1\r\n";
    $header .= "Host:".$this->host."\r\n";
    $header .= "User-Agent: ".$this->user_agent."\r\n";
    $header .= "Connection: close\r\n";
    $header .= "Referer: ".$this->protocol."://".$this->host.$this->path."\r\n\r\n";
    // Send the header
    fputs ($socket, $header);
    // Get the responce
    $res = "";

    // The PHP-recommended construction
    //    while (!feof($fp)) { $res .= fread($fp, 4096); };
    // hangs indefinitely on www.searchscout.com, for example.
    // seems that they do not close conection on their side or somewhat similar;

    // let's assume that there will be no HTML pages greater than 1 Mb

    $res = fread($socket, 1024*1024);

    // Close connection handle, we do not need it anymore
    fclose($socket);

    return $res;
  }

  function _process_code($res, $used_get = false) {
    error_log("Status code:".$this->code);

    switch ($this->code) {
    case '200': // OK
      if (preg_match('/(.*?)\r\n\r\n(.*)/s',$res,$matches)) {
        $this->headers = $matches[1];
      };

      $this->content = @file_get_contents($this->url);

      return true;
      break;
    case '301': // Moved Permanently
      $this->redirects++;
      if ($this->redirects > MAX_REDIRECTS) { return false; };
      preg_match('/Location: ([\S]+)/i',$res,$matches);
      return $this->fetch($this->_fix_location($matches[1]));
    case '302': // Found
      $this->redirects++;
      if ($this->redirects > MAX_REDIRECTS) { return false; };
      preg_match('/Location: ([\S]+)/i',$res,$matches);
      error_log('Redirected to:'.$matches[1]);
      return $this->fetch($this->_fix_location($matches[1]));
    case '400': // Bad request
    case '401': // Unauthorized
    case '402': // Payment required
    case '403': // Forbidden
    case '404': // Not found - but should return some html content - error page
      if (!preg_match('/(.*?)\r\n\r\n(.*)/s',$res,$matches)) {
        error_log("Unrecognized HTTP response");
        return false;
      };
      $this->headers = $matches[1];
      $this->content = @file_get_contents($this->url);
      return true;
    case '405': // Method not allowed; some sites (like MSN.COM) do not like "HEAD" HTTP requests
      // Try to get URL information using GET request (if we didn't tried it before)
      if (!$used_get) {
        $res = $this->_get();
        if (is_null($res)) { return null; };
        $this->code = $this->_extract_code($res);
        return $this->_process_code($res, true);
      } else {
        if (!preg_match('/(.*?)\r\n\r\n(.*)/s',$res,$matches)) {
          error_log("Unrecognized HTTP response");
          return false;
        };
        $this->headers = $matches[1];
        $this->content = @file_get_contents($this->url);
        return true;
      };
    default:
      error_log("Unrecognized HTTP result code:".$this->code);
      return false;
    };
  }}
?>
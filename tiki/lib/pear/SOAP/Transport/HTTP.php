<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: HTTP.php,v 1.31 2003/04/20 01:05:34 shane Exp $
//

/**
 * HTTP Transport class
 *
 * @package  SOAP
 * @category Web_Services
 */

/**
 * Needed Classes
 */
require_once 'SOAP/Base.php';
require_once 'Net/DIME.php';

/**
 *  HTTP Transport for SOAP
 *
 * @access public
 * @version $Id: HTTP.php,v 1.31 2003/04/20 01:05:34 shane Exp $
 * @package SOAP::Transport::HTTP
 * @author Shane Caraveo <shane@php.net>
 */
class SOAP_Transport_HTTP extends SOAP_Base_Object
{
    /**
     * Basic Auth string
     *
     * @var  string
     */
    var $headers = array();
    var $cookies;
    /**
     *
     * @var  int connection timeout in seconds - 0 = none
     */
    var $timeout = 4;

    /**
     * Array containing urlparts - parse_url()
     *
     * @var  mixed
     */
    var $urlparts = NULL;

    /**
     * Connection endpoint - URL
     *
     * @var  string
     */
    var $url = '';

    /**
     * Incoming payload
     *
     * @var  string
     */
    var $incoming_payload = '';

    /**
     * HTTP-Request User-Agent
     *
     * @var  string
     */
    var $_userAgent = SOAP_LIBRARY_NAME;

    /**
     * HTTP Encoding
     *
     * @var string
     */
    var $encoding = SOAP_DEFAULT_ENCODING;

    /**
     * HTTP-Response Content-Type encoding
     *
     * we assume UTF-8 if no encoding is set
     * @var  string
     */
    var $result_encoding = 'UTF-8';

    /**
     * HTTP-Response Content-Type
     */
    var $result_content_type;

    var $result_headers = array();
    var $result_cookies = array();
    /**
     * SOAP_Transport_HTTP Constructor
     *
     * @param string $URL    http url to soap endpoint
     *
     * @access public
     * @param  string $URI
     * @param  string $encoding  encoding to use
     */
    function SOAP_Transport_HTTP($URL, $encoding = SOAP_DEFAULT_ENCODING)
    {
        parent::SOAP_Base_Object('HTTP');
        $this->urlparts = @parse_url($URL);
        $this->url = $URL;
        $this->encoding = $encoding;
    }

    /**
     * send and receive soap data
     *
     * @param string &$msg       outgoing post data
     * @param string $action      SOAP Action header data
     * @param int    $timeout  socket timeout, default 0 or off
     *
     * @return string|fault response
     * @access public
     */
    function &send(&$msg,  /*array*/ $options = NULL)
    {
        $this->headers = array();
        if (!$this->_validateUrl()) {
            return $this->fault;
        }

        if (isset($options['timeout']))
            $this->timeout = (int)$options['timeout'];

        if (strcasecmp($this->urlparts['scheme'], 'HTTP') == 0) {
            return $this->_sendHTTP($msg, $options);
        } else if (strcasecmp($this->urlparts['scheme'], 'HTTPS') == 0) {
            return $this->_sendHTTPS($msg, $options);
        }

        return $this->_raiseSoapFault('Invalid url scheme '.$this->url);
    }

    /**
     * set data for http authentication
     * creates Authorization header
     *
     * @param string $username   username
     * @param string $password   response data, minus http headers
     *
     * @return none
     * @access public
     */
    function setCredentials($username, $password)
    {
        $this->headers['Authorization'] = 'Basic ' . base64_encode($username . ':' . $password);
    }

    function addCookie($name, $value)
    {
        $this->cookies[$name]=$value;
    }

    function _genCookieHeader()
    {
        foreach ($this->cookies as $name=>$value) {
            $cookies = (isset($cookies) ? $cookies. '; ' : '') .
                        urlencode($name) . '=' . urlencode($value);
        }
        return $cookies;
    }
    
    // private members

    /**
     * validate url data passed to constructor
     *
     * @return boolean
     * @access private
     */
    function _validateUrl()
    {
        if ( ! is_array($this->urlparts) ) {
            $this->_raiseSoapFault("Unable to parse URL $url");
            return FALSE;
        }
        if (!isset($this->urlparts['host'])) {
            $this->_raiseSoapFault("No host in URL $url");
            return FALSE;
        }
        if (!isset($this->urlparts['port'])) {

            if (strcasecmp($this->urlparts['scheme'], 'HTTP') == 0)
                $this->urlparts['port'] = 80;
            else if (strcasecmp($this->urlparts['scheme'], 'HTTPS') == 0)
                $this->urlparts['port'] = 443;

        }
        if (isset($this->urlparts['user'])) {
            $this->setCredentials(urldecode($this->urlparts['user']),
                                    urldecode($this->urlparts['pass']));
        }
        if (!isset($this->urlparts['path']) || !$this->urlparts['path'])
            $this->urlparts['path'] = '/';
        return TRUE;
    }

    /**
     * Finds out what is the encoding.
     *
     * Sets the object property accordingly.
     *
     * @access private
     * @param  array $headers  headers
     * @return void
     */
    function _parseEncoding($headers)
    {
        $h = stristr($headers,'Content-Type');
        preg_match('/^Content-Type:\s*(.*)$/im',$h,$ct);
        $this->result_content_type = str_replace("\r","",$ct[1]);
        if (preg_match('/(.*?)(?:;\s?charset=)(.*)/i',$this->result_content_type,$m)) {
            // strip the string of \r
            $this->result_content_type = $m[1];
            if (count($m) > 2) {
                $enc = strtoupper(str_replace('"',"",$m[2]));
                if (in_array($enc, $this->_encodings)) {
                    $this->result_encoding = $enc;
                }
            }
        }
        // deal with broken servers that don't set content type on faults
        if (!$this->result_content_type) $this->result_content_type = 'text/xml';
    }

    function _parseHeaders($headers)
    {
        /* largely borrowed from HTTP_Request */
        $this->result_headers = array();
        $headers = split("\r?\n", $headers);
        foreach ($headers as $value) {
            if (strpos($value,':') === false) {
                $this->result_headers[0]=$value;
                continue;
            }
            list($name,$value) = split(':',$value);
            $headername = strtolower($name);
            $headervalue = trim($value);
            $this->result_headers[$headername]=$headervalue;
            
            if ($headername == 'set-cookie') {
                // Parse a SetCookie header to fill _cookies array
                $cookie = array(
                    'expires' => null,
                    'domain'  => $this->urlparts['host'],
                    'path'    => null,
                    'secure'  => false
                );

                // Only a name=value pair
                if (!strpos($headervalue, ';')) {
                    list($cookie['name'], $cookie['value']) = array_map('trim', explode('=', $headervalue));
                    $cookie['name']  = urldecode($cookie['name']);
                    $cookie['value'] = urldecode($cookie['value']);

                // Some optional parameters are supplied
                } else {
                    $elements = explode(';', $headervalue);
                    list($cookie['name'], $cookie['value']) = array_map('trim', explode('=', $elements[0]));
                    $cookie['name']  = urldecode($cookie['name']);
                    $cookie['value'] = urldecode($cookie['value']);

                    for ($i = 1; $i < count($elements);$i++) {
                        list ($elName, $elValue) = array_map('trim', explode('=', $elements[$i]));
                        if ('secure' == $elName) {
                            $cookie['secure'] = true;
                        } elseif ('expires' == $elName) {
                            $cookie['expires'] = str_replace('"', '', $elValue);
                        } elseif ('path' == $elName OR 'domain' == $elName) {
                            $cookie[$elName] = urldecode($elValue);
                        } else {
                            $cookie[$elName] = $elValue;
                        }
                    }
                }
                $this->result_cookies[] = $cookie;
            }
        }
    }
    
    /**
     * Remove http headers from response
     *
     * @return boolean
     * @access private
     */
    function _parseResponse()
    {
        if (preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $this->incoming_payload, $match)) {
            #$this->response = preg_replace("/[\r|\n]/", '', $match[2]);
            $this->response =& $match[2];
            // find the response error, some servers response with 500 for soap faults
            $this->_parseHeaders($match[1]);
    
            list($protocol, $code) = sscanf($this->result_headers[0], '%s %s');
            unset($this->result_headers[0]);            
            if ($code >= 400 && $code < 500) {
                    $this->_raiseSoapFault("HTTP Response $code Not Found");
                    return FALSE;
            }
            $this->_parseEncoding($match[1]);
            if ($this->result_content_type == 'application/dime') {
                // XXX quick hack insertion of DIME
                $this->_decodeDIMEMessage($this->response,$this->headers,$this->attachments);
                $this->result_content_type = $this->headers['content-type'];
            } else if (stristr($this->result_content_type,'multipart/related')) {
                $this->response = $this->incoming_payload;
                $this->_decodeMimeMessage($this->response,$this->headers,$this->attachments);
            } else if ($this->result_content_type != 'text/xml') {
                $this->_raiseSoapFault($this->response);
                return FALSE;
            }
            // if no content, return false
            return strlen($this->response) > 0;
        }
        $this->_raiseSoapFault('Invalid HTTP Response');
        return FALSE;
    }

    /**
     * Create http request, including headers, for outgoing request
     *
     * @param string   &$msg   outgoing SOAP package
     * @param $options
     * @return string outgoing_payload
     * @access private
     */
    function &_getRequest(&$msg, $options)
    {
        $action = isset($options['soapaction'])?$options['soapaction']:'';
        $fullpath = $this->urlparts['path'].
                        (isset($this->urlparts['query'])?'?'.$this->urlparts['query']:'').
                        (isset($this->urlparts['fragment'])?'#'.$this->urlparts['fragment']:'');
        if (isset($options['proxy_host'])) {
            $fullpath = 'http://' . $this->urlparts['host'] . ':' . $this->urlparts['port'] . $fullpath;
        }
        if (isset($options['proxy_user'])) {
            $this->headers['Proxy-Authorization'] = 'Basic ' . base64_encode($options['proxy_user'].":".$options['proxy_pass']);
        }
        $this->headers['User-Agent'] = $this->_userAgent;
        $this->headers['Host'] = $this->urlparts['host'];
        $this->headers['Content-Type'] = "text/xml; charset=$this->encoding";
        $this->headers['Content-Length'] = strlen($msg);
        $this->headers['SOAPAction'] = "\"$action\"";
        if (isset($options['headers'])) {
            $this->headers = array_merge($this->headers, $options['headers']);
        }
        
        $this->cookies = array();
        if (!isset($options['nocookies']) || !$options['nocookies']) {
            // add the cookies we got from the last request
            if (isset($this->result_cookies)) {
                foreach ($this->result_cookies as $cookie) {
                    if ($cookie['domain'] == $this->urlparts['host'])
                        $this->cookies[$cookie['name']]=$cookie['value'];
                }
            }
        }
        // add cookies the user wants to set
        if (isset($options['cookies'])) {
            foreach ($options['cookies'] as $cookie) {
                if ($cookie['domain'] == $this->urlparts['host'])
                    $this->cookies[$cookie['name']]=$cookie['value'];
            }
        }
        if (count($this->cookies)) {
            $this->headers['Cookie'] = $this->_genCookieHeader();
        }
        $headers = '';
        foreach ($this->headers as $k => $v) {
            $headers .= "$k: $v\r\n";
        }
        $this->outgoing_payload =
                "POST $fullpath HTTP/1.0\r\n".
                $headers."\r\n".
                $msg;
        return $this->outgoing_payload;
    }

    /**
     * Send outgoing request, and read/parse response
     *
     * @param string  &$msg   outgoing SOAP package
     * @param string  $action   SOAP Action
     * @return string &$response   response data, minus http headers
     * @access private
     */
    function &_sendHTTP(&$msg, $options)
    {
        $this->incoming_payload = '';
        $this->_getRequest($msg, $options);
        $host = $this->urlparts['host'];
        $port = $this->urlparts['port'];
        if (isset($options['proxy_host'])) {
            $host = $options['proxy_host'];
            $port = isset($options['proxy_port']) ? $options['proxy_port'] : 8080;
        }
        // send
        if ($this->timeout > 0) {
            $fp = @fsockopen($host, $port, $this->errno, $this->errmsg, $this->timeout);
        } else {
            $fp = @fsockopen($host, $port, $this->errno, $this->errmsg);
        }
        if (!$fp) {
            return $this->_raiseSoapFault("Connect Error to $host:$port");
        }
        if ($this->timeout > 0) {
            // some builds of php do not support this, silence
            // the warning
            @socket_set_timeout($fp, $this->timeout);
        }
        if (!fputs($fp, $this->outgoing_payload, strlen($this->outgoing_payload))) {
            return $this->_raiseSoapFault("Error POSTing Data to $host");
        }

        // get reponse
        // XXX time consumer
        while ($data = fread($fp, 32768)) {
            $this->incoming_payload .= $data;
        }

        fclose($fp);

        if (!$this->_parseResponse()) {
            return $this->fault;
        }
        return $this->response;
    }

    /**
     * Send outgoing request, and read/parse response, via HTTPS
     *
     * @param string  &$msg   outgoing SOAP package
     * @param string  $action   SOAP Action
     * @return string &$response   response data, minus http headers
     * @access private
     */
    function &_sendHTTPS(&$msg, $options)
    {
        /* NOTE This function uses the CURL functions
         *  Your php must be compiled with CURL
         */
        if (!extension_loaded('curl')) {
            return $this->_raiseSoapFault('CURL Extension is required for HTTPS');
        }

//        $this->_getRequest($msg, $options);

        $ch = curl_init();

        // XXX don't know if this proxy stuff is right for CURL
        // Arnaud: apparently it is, we have a proxy and it works
        // with these lines.
        if (isset($options['proxy_host'])) {
            // $options['http_proxy'] == 'hostname:port'
            $host = $options['proxy_host'];
            $port = isset($options['proxy_port']) ? $options['proxy_port'] : 8080;
            curl_setopt($ch, CURLOPT_PROXY, $host . ":" . $port);
        }

        if (isset($options['proxy_user'])) {
            // $options['http_proxy_userpw'] == 'username:password'
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['proxy_user'] . ':' . $options['proxy_pass']);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER ,    array('Content-Type: text/xml;charset=' . $this->encoding, 'SOAPAction: ""'));
        curl_setopt($ch, CURLOPT_USERAGENT ,     $this->_userAgent);

        if ($this->timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); //times out after 4s
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS,     $msg);
        curl_setopt($ch, CURLOPT_URL,            $this->url);
        curl_setopt($ch, CURLOPT_POST,           1);
        curl_setopt($ch, CURLOPT_FAILONERROR,    1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE,        1);

        if (isset($options['curl'])) {
            reset($options['curl']);
            while (list($key, $val) = each ($options['curl'])) {
                curl_setopt($ch, $key, $val);
            }
        }

        $this->response = curl_exec($ch);
        if (! $this->response) {
            $m = 'curl_exec error ' . curl_errno($ch) . ' ' . curl_error($ch);
            curl_close($ch);
            return $this->_raiseSoapFault($m);
        }        
        curl_close($ch);

        return $this->response;
    }
} // end SOAP_Transport_HTTP
?>
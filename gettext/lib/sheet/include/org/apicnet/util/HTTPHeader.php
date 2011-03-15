<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/*This file is part of J4PHP - Ensembles de propriétés et méthodes permettant le developpment rapide d'application web modulaire
Copyright (c) 2002-2004 @PICNet

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU LESSER GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU LESSER GENERAL PUBLIC LICENSE
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/**
 * HTTPHeader
 * 
 * @package 
 * @author diogene
 * @copyright Copyright (c) 2004
 * @version $Id: HTTPHeader.php,v 1.3 2005-05-18 11:01:40 mose Exp $
 * @access public
 **/
class HTTPHeader {
	var $_var = array();
	
	/**
	 * Constructeur
	 * 
	 * @access public 
	 * @param name $ string
	 * @return mixed 
	 * @todo change order (POST normally overrides GET)
	 */	
	function HTTPHeader(){
		if (sizeof($_GET)>0) $this->_var = array_merge($this->_var, $_GET);
		if (sizeof($_POST)>0) $this->_var = array_merge($this->_var, $_POST);
	}
	
	/**
	 * Get request variable
	 * 
	 * @access public 
	 * @param name $ string
	 * @return mixed 
	 * @todo change order (POST normally overrides GET)
	 */
	function &RequestGetVar($name){
		if (isset($this->_var[$name])) {
		    return $this->_var[$name];
		}else {
			return FALSE;
		}
	}
	
	/**
	 * add request variable
	 * 
	 * @access public 
	 * @param name $ string
	 * @return mixed 
	 * @todo change order (POST normally overrides GET)
	 */
	function addGetVar($name, $value){
		if (isset($this->_var[$name])) {
		    return FALSE;
		}else {
			$this->_var[$name] = $value;
		}
	}	
	// SERVER FUNCTIONS
	/**
	 * Gets a server variable
	 * 
	 * Returns the value of $name server variable.
	 * Accepted values for $name are exactly the ones described by the
	 * {@link http://www.php.net/manual/en/reserved.variables.html#reserved.variables.server PHP manual}.
	 * If the server variable doesn't exist void is returned.
	 * 
	 * @author Marco Canini <m.canini@libero.it> 
	 * @access public 
	 * @param name $ string the name of the variable
	 * @return mixed value of the variable
	 */
	function ServerGetVar($name) { 
		// Try the new stuff first, see link above
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		} 
		// Make it work with older php versions
		// FIXME: 4.1.2 is our requirement, superglobals were available
		// in 4.1.0 and higher, i think we can move this out.
		if (isset($GLOBALS['HTTP_SERVER_VARS'][$name])){
			return $GLOBALS['HTTP_SERVER_VARS'][$name];
		} 
		if (isset($_ENV[$name])){
			return $_ENV[$name];
		} 
		// FIXME: 4.1.2 is our requirement, superglobals were available
		// in 4.1.0 and higher, i think we can move this out.
		if (isset($GLOBALS['HTTP_ENV_VARS'][$name])){
			return $HTTP_ENV_VARS[$name];
		} 
		if ($val = getenv($name)){
			return $val;
		} 
		return; // we found nothing here
	}

	/**
	 * Get base URI for Xaraya
	 * 
	 * @access public 
	 * @return string base URI for Xaraya
	 * @todo remove whatever may come after the PHP script - TO BE CHECKED !
	 * @todo See code comments.
	 */
	function ServerGetBaseURI(){ 
		// Get the name of this URI
		$path = $this->ServerGetVar('REQUEST_URI'); 
		// if ((empty($path)) ||
		// (substr($path, -1, 1) == '/')) {
		// what's wrong with a path (cfr. Indexes index.php, mod_rewrite etc.) ?
		if (empty($path)){ 
			// REQUEST_URI was empty or pointed to a path
			// adapted patch from Chris van de Steeg for IIS
			// Try SCRIPT_NAME
			$path = $this->ServerGetVar('SCRIPT_NAME');
			if (empty($path)){ 
				// No luck there either
				// Try looking at PATH_INFO
				$path = $this->ServerGetVar('PATH_INFO');
			} 
		} 
		$path = preg_replace('/[#\?].*/', '', $path);
		$path = preg_replace('/\.php\/.*$/', '', $path);
		if (substr($path, -1, 1) == '/'){
			$path .= 'dummy';
		} 
		$path = dirname($path);
		
		if (preg_match('!^[/\\\]*$!', $path)){
			$path = '';
		} 
	
		return $path;
	}

	/**
	 * Gets the host name
	 * 
	 * Returns the server host name fetched from HTTP headers when possible.
	 * The host name is in the canonical form (host + : + port) when the port is different than 80.
	 * 
	 * @author Marco Canini <m.canini@libero.it> 
	 * @access public 
	 * @return string HTTP host name
	 */
	function ServerGetHost(){
		$server = $this-> ServerGetVar('HTTP_HOST');
		if (empty($server)){ 
			// HTTP_HOST is reliable only for HTTP 1.1
			$server =  $this->ServerGetVar('SERVER_NAME');
			$port =  $this->ServerGetVar('SERVER_PORT');
			 if ($port != '80') $server .= ":$port";
		} 
		return $server;
	} 

	/**
	 * Gets the current protocol
	 * 
	 * Returns the HTTP protocol used by current connection, it could be 'http' or 'https'.
	 * 
	 * @author Marco Canini <m.canini@libero.it> 
	 * @access public 
	 * @return string current HTTP protocol
	 */
	function ServerGetProtocol(){
		$HTTPS =  $this->ServerGetVar('HTTPS');
		 // IIS seems to set HTTPS = off for some reason
		return (!empty($HTTPS) && $HTTPS != 'off') ? 'https' : 'http';
	} 
	
	/**
	 * get base URL for Xaraya
	 * 
	 * @access public 
	 * @returns string
	 * @return base URL for Xaraya
	 */
	function ServerGetBaseURL(){
		$server =  $this->ServerGetHost();
		$protocol =  $this->ServerGetProtocol();
		$path =  $this->ServerGetBaseURI();
		
		return "$protocol://$server$path/";
	} 

	/**
	 * Get current URL (and optionally add/replace some parameters)
	 * 
	 * @access public 
	 * @param args $ array additional parameters to be added to/replaced in the URL (e.g. theme, ...)
	 * @return string current URL
	 * @todo cfr. BaseURI() for other possible ways, or try PHP_SELF
	 */
	function ServerGetCurrentURL($args = array()) {
		 // get current URI
		$request =  $this->ServerGetVar('QUERY_STRING');
		
		// Note to Dracos: please don't replace & with &amp; here just yet - give me some time to test this first :-)
		// add optional parameters
		if (count($args) > 0) {
			$request .= '&';
			foreach ($args as $k => $v){
				if (is_array($v)){
					foreach($v as $l => $w){ 
						// TODO: replace in-line here too ?
						if (!empty($w)) $request .= $k . "[$l]=$w&";
					} 
				} else {
					// if this parameter is already in the query string...
					if (preg_match("/(&|\?)($k=[^&]*)/", $request, $matches)){
						$find = $matches[2];
					 	// ... replace it in-line if it's not empty
						if (!empty($v)){
							$request = preg_replace("/(&|\?)$find/", "$1$k=$v", $request);
							 // ... or remove it otherwise
						} elseif ($matches[1] == '?'){
							$request = preg_replace("/\?$find(&|)/", '?', $request);
						} else{
							$request = preg_replace("/&$find/", '', $request);
						} 
					} elseif (!empty($v)){
						$request .= "$k=$v&";
					} 
				} 
			} 
			$request = substr($request, 0, -1);
		} 
		return $request;
	}
	
	/**
	 * Check to see if this is a local referral
	 * 
	 * @access public 
	 * @return bool true if locally referred, false if not
	 */
	function RequestIsLocalReferer(){
		$server = ServerGetHost();
		$referer = ServerGetVar('HTTP_REFERER');
	
		if (!empty($referer) && preg_match("!^https?://$server(:\d+|)/!", $referer)){
			return true;
		} else{
			return false;
		} 
	} 
} 

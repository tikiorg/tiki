<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

class TikiInit
{

	function TikiInit() {
	}


/** Return 'windows' if windows, otherwise 'unix'
  * \static
  */
	function os() {
		static $os;
		if (!isset($os)) {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$os = 'windows';
			} else {
				$os = 'unix';
			}
		}
		return $os;
	}


/** Return true if windows, otherwise false
  * \static
  */
	static function isWindows() {
		static $windows;
		if (!isset($windows)) {
			$windows = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
		}
		return $windows;
	}
	
	/*
	 * @param string $path	directory to test
	 * @param bool $is_file	default false for a dir
	 * @return bool
	 *
	 * Copes with Windows premissions
	 */
	static function is_writeable($path) {
		if (self::isWindows()) {
			return self::is__writable($path);
		} else {
			return is_writeable($path);
		}
	}

	/*
	 * @param string $path	directory to test	NOTE: use a trailing slash for folders!!!
	 * @return bool
	 * 
	 * From the php is_writable manual (thanks legolas558 d0t users dot sf dot net)
	 * Note the two underscores and no "e"
	 */
	static function is__writable($path) {
		//will work in despite of Windows ACLs bug
		//NOTE: use a trailing slash for folders!!!
		//see http://bugs.php.net/bug.php?id=27609
		//see http://bugs.php.net/bug.php?id=30931

		if ($path{strlen($path)-1}=='/') { // recursively return a temporary file path
			return self::is__writable($path.uniqid(mt_rand()).'.tmp');
		} else if (is_dir($path)) {
			return self::is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
		}
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}


/** Prepend $path to the include path
  * \static
  */
	static function prependIncludePath($path) {
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path = $path . PATH_SEPARATOR . $include_path;
		} else if (!$include_path) {
			$include_path = $path;
		} 

		return set_include_path ($include_path);
	}


/** Append $path to the include path
  * \static
  */
	static function appendIncludePath($path) {
		$include_path = ini_get('include_path');
		$paths = explode(PATH_SEPARATOR, $include_path);

		if ($include_path && !in_array($path, $paths)) {
			$include_path .= PATH_SEPARATOR . $path;
		} else if (!$include_path) {
			$include_path = $path;
		} 

		return set_include_path ($include_path);
	}


/** Return system defined temporary directory.
  * In Unix, this is usually /tmp
  * In Windows, this is usually c:\windows\temp or c:\winnt\temp
  * \static
  */
	static function tempdir() {
		static $tempdir;
		if (!$tempdir) {
			$tempfile = tempnam(false,'');
			$tempdir = dirname($tempfile);
			@unlink($tempfile);
		}
		return $tempdir;
	}

	/**
	*	Determine if the web server is an IIS server
	*	@return true if IIS server, else false
  	* \static
	*/
	static function isIIS() {
		static $IIS;

		// Sample value Microsoft-IIS/7.5
		if (!isset($IIS) && isset($_SERVER['SERVER_SOFTWARE'])) {
			$IIS = substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS';
		}

		return $IIS;
	}


	/**
	*	Build the full URL for the current page
	*	Helper function for checkIISFileAccess
	*	@return Full URL to the current page
  	* \static
	*/
	static function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) 
			$pageURL .= 's';
		$pageURL .= '://';
		if ($_SERVER['SERVER_PORT'] != '80') {
			$pageURL .= $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}


	/**
	*	Build the Tiki base URL
	*	Helper function for checkIISFileAccess
	*	@return The tiki base folder URL. Not including the "/"
  	* \static
	*/
	static function tikiBaseURL() {
		$baseURL = self::curPageURL();
		$fileName = basename($_SERVER['SCRIPT_NAME']);
		$pos = strpos($baseURL,$fileName)-1;
		if($pos <0)
			return $baseURL;
		return substr($baseURL,0,$pos);
	}

	
	/**
	*	If running IIS, check the file access in folders with web.config with a known "problem file"
	*	If the URL Rewrite module missing the file will fail to load
	*
	*	@return false if IIS file access seems to fail (with a valid check). Otherwise true
  	* \static
	*/
	static function checkIISFileAccess()
	{
		require_once 'Zend/Http/Client.php';	
		static	$rcAccess;
		
		// A small file that fails when URL Rewrite is not present
		$checkFile = 'lib/metrics.js';

		if(!isset($rcAccess)) {

			try {
				// Build the URL
				$baseURL = self::tikiBaseURL();
				$checkURL = $baseURL."/".$checkFile;
			
				// Do the check
				$client = new Zend_Http_Client($checkURL, 
						array('maxredirects' => 0,
							'timeout'      => 10));
				if($client != null) {
					$response = $client->request('GET');
					if($response->isError())
						$rcAccess = false;	// Probably no URL Rewrite present
					else
						$rcAccess = true; 	// URL Rewrite present?
				}
	
			} catch(Zend_Http_Client_Exception $e) {
				$err = $e->getMessage();
			}
			if(!isset($rcAccess)) 
				return true; // Something when wrong. Assume it's not caused by a missing URL Rewrite module

		}
		return $rcAccess;
	}


	/**
	*	Build a warning string if IIS is used. It requires the URL Rewrite module for proper operation.
	*	@return IIS warning string. An empty string is returned for other servers.
  	* \static
	*/
	static function buildIISWarning() {
		// Prepare IIS warning string
		$iis_warning = '';
		if(TikiInit::isIIS()) {
			$iis_warning = '
				<br/>
				<h3>IIS Installation Note</h3>
				You seem to be installing on an IIS server.<br>
				For proper operation on IIS the <b>URL Rewrite</b> module should be installed. 
				Without it you will be able to operate Tiki, but you may encounter some problems with images and some features.</p>
				<p>
				<a href="http://go.microsoft.com/?linkid=9722533">Download the x86 version of the URL Rewrite module for IIS7.</a><br />
				<a href="http://go.microsoft.com/?linkid=9722532">Download the x64 version of the URL Rewrite module for IIS7.</a>
				</p>
				<p>
				If you do not install the <b>URL Rewrite</b> module, you may want to rename the web.config files in the various Tiki folders (e.g. to web_config).</p>';

			if(!TikiInit::checkIISFileAccess()) {
				$iis_warning .= '
					<p>
					Status: <span style="color:red">Your system does <b>not</b> seem to have the <b>URL Rewrite</b> module installed.</span><br/>
					It is highly recommended that you
					<ol>
					<li>Quit this installer</li>
					<li>Install the URL Rewrite module (recommended) / Rename the web.config file</li>
					<li>Restart this installer</li>
					</ol></span>
					</p>';
			} else {
				$iis_warning .= '
					<p>
					Status: <span style="font-weight:bold"><u>Your system seems to have the <b>URL Rewrite</b> module installed</u>.
					</p>';		
			}
			$iis_warning .= '<p>&nbsp;</p>';
			
			$iis_warning = "<div style='text-align:left'>".$iis_warning."</div>";
		}
		return $iis_warning;
	}	
}

function tiki_error_handling($errno, $errstr, $errfile, $errline) {
	global $prefs, $phpErrors;

	$err[E_ERROR]           = 'E_ERROR';
	$err[E_CORE_ERROR]      = 'E_CORE_ERROR';
	$err[E_USER_ERROR]      = 'E_USER_ERROR';
	$err[E_COMPILE_ERROR]   = 'E_COMPILE_ERROR';
	$err[E_WARNING]         = 'E_WARNING';
	$err[E_CORE_WARNING]    = 'E_CORE_WARNING';
	$err[E_USER_WARNING]    = 'E_USER_WARNING';
	$err[E_COMPILE_WARNING] = 'E_COMPILE_WARNING';
	$err[E_PARSE]           = 'E_PARSE';
	$err[E_NOTICE]          = 'E_NOTICE';
	$err[E_USER_NOTICE]     = 'E_USER_NOTICE';
	$err[E_STRICT]          = 'E_STRICT';

	if ( !defined('E_RECOVERABLE_ERROR') ) define('E_RECOVERABLE_ERROR', 4096);
	$err[E_RECOVERABLE_ERROR] = 'E_RECOVERABLE_ERROR';

	if ( !defined('E_DEPRECATED') ) define('E_DEPRECATED', 8192);
	$err[E_DEPRECATED] = 'E_DEPRECATED';

	if ( !defined('E_USER_DEPRECATED') ) define('E_USER_DEPRECATED', 16384);
	$err[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';

	global $tikipath;
	$errfile = str_replace($tikipath, '', $errfile);
	switch ($errno) {
	case E_ERROR:
	case E_CORE_ERROR:
	case E_USER_ERROR:
	case E_COMPILE_ERROR:
	case E_WARNING:
	case E_CORE_WARNING:
	case E_USER_WARNING:
	case E_COMPILE_WARNING:
	case E_PARSE:
	case E_RECOVERABLE_ERROR:
		$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
		$back.= "<b>PHP (".PHP_VERSION.") ERROR (".$err[$errno]."):</b><br />";
		$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
		$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
		$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
		$back.= "</div>";
		$phpErrors[] = $back;
		break;
	case E_STRICT:
	case E_NOTICE:
	case E_USER_NOTICE:
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
		if (!  defined('THIRD_PARTY_LIBS_PATTERN') ||  ! preg_match(THIRD_PARTY_LIBS_PATTERN, $errfile) ) {
			if ($prefs['smarty_notice_reporting'] != 'y' && strstr($errfile, '.tpl.php'))
				break;
			$back = "<div class='rbox-data' style='font-size:10px;border:1px solid'>";
			$back.= "<b>PHP (".PHP_VERSION.") NOTICE ($err[$errno]):</b><br />";
			$back.= "<b style='font-family: monospace'>File:</b> $errfile<br />";
			$back.= "<b style='font-family: monospace'>Line:</b> $errline<br />";
			$back.= "<b style='font-family: monospace'>Type:</b> $errstr";
			$back.= "</div>";
			$phpErrors[] = $back;
		}
	default:
		break;
	}
}

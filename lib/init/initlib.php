<?php // $Id: initlib.php,v 1.13 2006-11-29 10:39:10 mose Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

class TikiInit {

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
	function isWindows() {
		static $windows;
		if (!isset($windows)) {
			$windows = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
		}
		return $windows;
	}


/** Return ';' if windows otherwise ':'
  * \static
  */
	function pathSeparator() {
		static $separator;
		if (!isset($separator)) {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$separator = ';';
			} else {
				$separator = ':';
			}
		}
		return $separator;
	}


/** Prepend $path to the include path
  * \static
  */
	function prependIncludePath($path) {
		$include_path = ini_get('include_path');
		if ($include_path) {
			$include_path = $path . TikiInit::pathSeparator(). $include_path;
		} else {
			$include_path = $path;
		}
		return ini_set('include_path', $include_path);
	}


/** Append $path to the include path
  * \static
  */
	function appendIncludePath($path) {
		$include_path = ini_get('include_path');
		if ($include_path) {
			$include_path .= TikiInit::pathSeparator(). $path;
		} else {
			$include_path = $path;
		}
		return ini_set('include_path', $include_path);
	}


/** Return system defined temporary directory.
  * In Unix, this is usually /tmp
  * In Windows, this is usually c:\windows\temp or c:\winnt\temp
  * \static
  */
	function tempdir() {
		static $tempdir;
		if (!$tempdir) {
			$tempfile = tempnam(false,'');
			$tempdir = dirname($tempfile);
			@unlink($tempfile);
		}
		return $tempdir;
	}

}

function tiki_error_handling($errno, $errstr, $errfile, $errline) {
	global $error_reporting_level,$tiki_p_admin,$phpErrors;
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
	if (!empty($error_reporting_level) and $error_reporting_level) {
		$errfile = basename($errfile);
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
			if ($error_reporting_level == 2047 or $error_reporting_level == 2039 or ($error_reporting_level == 1 and $tiki_p_admin == 'y')) {
				$back = "<div style='padding:4px;border:1px solid #000;background-color:#F66;font-size:10px;'>";
				$back.= "<b>PHP (".PHP_VERSION.") ERROR (".$err[$errno]."):</b><br />";
				$back.= "<tt><b>File:</b></tt> $errfile<br />";
				$back.= "<tt><b>Line:</b></tt> $errline<br />";
				$back.= "<tt><b>Type:</b></tt> $errstr";
				$back.= "</div>";
				$phpErrors[] = $back;
			}
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			if ($error_reporting_level == '2047' and $tiki_p_admin == 'y') {
				$back = "<div style='padding:4px;border:1px solid #000;background-color:#FF6;font-size:10px;'>";
				$back.= "<b>PHP (".PHP_VERSION.") NOTICE ($errno):</b><br />";
				$back.= "<tt><b>File:</b></tt> $errfile<br />";
				$back.= "<tt><b>Line:</b></tt> $errline<br />";
				$back.= "<tt><b>Type:</b></tt> $errstr";
				$back.= "</div>";
				$phpErrors[] = $back;
			}
		default:
			break;
		}
	}
}
set_error_handler("tiki_error_handling");

?>

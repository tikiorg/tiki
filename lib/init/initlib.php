<?

class TikiInit {

	function TikiInit() {
	}


/** Return 'windows' if windows, otherwise 'unix'
  * \static
  */
	function os() {
		static $os;
		if (!isset($os)) {
			if (substr(PHP_OS, 0, 3) == 'WIN') {
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
			$windows = substr(PHP_OS, 0, 3) == 'WIN';
		}
		return $windows;
	}


/** Return ';' if windows otherwise ':'
  * \static
  */
	function pathSeparator() {
		static $separator;
		if (!isset($separator)) {
			if (substr(PHP_OS, 0, 3) == 'WIN') {
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

?>

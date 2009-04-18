<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/* Automatically set params used for absolute URLs - BEGIN */

// Note: need to susbsitute \ for / for windows.
$tiki_setup_dir = str_replace('\\','/',realpath(dirname(__FILE__)));
$tiki_script_filename = str_replace('\\','/',getcwd());

if ($tiki_script_filename !== false) {
	$tiki_script_filename .= '/index.php';
} else {
	// Note: need to susbsitute \ for / for windows.
	$tiki_script_filename = $_SERVER['SCRIPT_FILENAME'];

	// On some systems, SCRIPT_FILENAME contains the full path to the cgi script
	// that calls the script we are looking for. In this case, we have to
	// fallback to PATH_TRANSLATED. This one may be wrong on some systems, this
	// is why SCRIPT_FILENAME is tried first.
	//
	// Note that PATH_TRANSLATED is not always set on PHP5, so try to get first value of get_included_files() in this case
	//
	if ( substr($tiki_script_filename, 0, strlen($tiki_setup_dir)) != $tiki_setup_dir ) {
		// Note: need to susbsitute \ for / for windows.
		$tiki_script_filename = empty($_SERVER['PATH_TRANSLATED']) ? current(get_included_files()) : $_SERVER['PATH_TRANSLATED'];
	}

	$tiki_script_filename = str_replace('\\', '/', realpath($tiki_script_filename));
}
$tmp = dirname(str_replace($tiki_setup_dir,'',$tiki_script_filename));

if ($tmp != '/') {
	$dir_level = substr_count($tmp,"/");
} else {
	$dir_level = 0;
}
unset($tmp);

// Note: need to susbsitute \ for / for windows.
$tikiroot = str_replace('\\','/',dirname($_SERVER['PHP_SELF']));
$tikipath = str_replace('\\','/',dirname($tiki_script_filename));
$tikipath = dirname($tiki_script_filename);

if ($dir_level > 0) {
	$tikiroot = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikiroot);
	$tikipath = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikipath);
	chdir(join('../',array_fill(0,$dir_level+1,'')));
}

if ( substr($tikiroot,-1,1) != '/' ) $tikiroot .= '/';
if ( substr($tikipath,-1,1) != '/' ) $tikipath .= '/';

require_once('lib/init/initlib.php');
TikiInit::prependIncludePath($tikipath.'lib/pear');
TikiInit::appendIncludePath($tikipath.'lib/core/lib');
require_once('lib/core/lib/DeclFilter.php');
require_once('lib/core/lib/JitFilter.php');

?>

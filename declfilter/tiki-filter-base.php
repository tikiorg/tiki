<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* Automatically set params used for absolute URLs - BEGIN */

$tiki_setup_dir = realpath(dirname(__FILE__));
$tiki_script_filename = realpath($_SERVER['SCRIPT_FILENAME']);

// On some systems, SCRIPT_FILENAME contains the full path to the cgi script that 
//   calls the script we are looking for. In this case, we have to fallback to 
//   PATH_TRANSLATED. This one may be wrong on some systems, this is why SCRIPT_FILENAME
//   is tried first.

if ( substr($tiki_script_filename, 0, strlen($tiki_setup_dir)) != $tiki_setup_dir ) {
	$tiki_script_filename = realpath($_SERVER['PATH_TRANSLATED']);
}
$tmp = dirname(str_replace($tiki_setup_dir,'',$tiki_script_filename));

if ($tmp != '/') {
        $dir_level = substr_count($tmp,"/");
} else {
        $dir_level = 0;
}
unset($tmp);

$tikiroot = dirname($_SERVER['PHP_SELF']);
$tikipath = dirname($tiki_script_filename);

if ($dir_level > 0) {
        $tikiroot = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikiroot);
        $tikipath = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikipath);
        chdir(join('../',array_fill(0,$dir_level+1,'')));
}

if ( substr($tikiroot,-1,1) != '/' ) $tikiroot .= '/';
if ( substr($tikipath,-1,1) != '/' ) $tikipath .= '/';

require_once('lib/init/initlib.php');
TikiInit::prependIncludePath($tikipath);
TikiInit::prependIncludePath('lib');
TikiInit::prependIncludePath('lib/pear');
TikiInit::prependIncludePath('lib/core/lib');

require_once 'DeclFilter.php';

?>

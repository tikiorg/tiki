<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/set_paths.php,v 1.1.2.2 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

/* Automatically set params used for absolute URLs - BEGIN */

$tmp = dirname(str_replace(dirname(dirname(dirname(__FILE__))),'',$_SERVER['SCRIPT_FILENAME']));
if ($tmp != '/') {
        $dir_level = substr_count($tmp,"/");
} else {
        $dir_level = 0;
}
unset($tmp);

$tikiroot = dirname($_SERVER['PHP_SELF']);
if ("\\" == $tikiroot) $tikiroot="/"; // even onv windows / is used!
$tikipath = dirname($_SERVER['SCRIPT_FILENAME']);

if ($dir_level > 0) {
        $tikiroot = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikiroot);
        $tikipath = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikipath);
        chdir(join('../',array_fill(0,$dir_level+1,'')));
}

if ( substr($tikiroot,-1,1) != '/' ) $tikiroot .= '/';
if ( substr($tikipath,-1,1) != '/' ) $tikipath .= '/';

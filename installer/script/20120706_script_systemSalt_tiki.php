<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20120706_script_systemSalt_tiki.php  2012-07-06 petjal $

//  Calculates and inserts $systemSalt_tiki into db/local.php.
//  Use after an svn up or svn switch to tiki 9.x
//  Usage:   sudo php installer/shell.php

//include('tiki-setup.php');

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
    header("location: index.php");
    exit;
}

//Sorry, not sure of which of the next few lines we need...
if (isset($_SERVER['REQUEST_METHOD'])) die;
if (!isset($_SERVER['argc']))
    die("Usage: php installer/shell.php <domain>\n");
if (!file_exists('db/local.php'))
    die("Tiki is not installed yet.\n");
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] != 'install' && $_SERVER['argv'][1] != 'skiperrors') {
    $_SERVER['TIKI_VIRTUAL'] = basename($_SERVER['argv'][1]);
}

//  .../installer/script/__FILE__
//  $tikipath = dirname(__FILE__) . '/../../';
if (!empty($multi)) {
    $local = "db/$multi/local.php";
} else {
    $local = 'db/local.php';
}

$systemSalt_tiki = '';
include ($local);

//print_r('$systemSalt_tiki=' . $systemSalt_tiki . " ; strlen: " . strlen($systemSalt_tiki) . "\n");

echo "Running update script 20120706_script_systemSalt_tiki.php...\n";

$saltLength = 100 ;  // from installer/installlib.php $GLOBALS['numRandomBytes']. Should make a preference.
if (strlen($systemSalt_tiki) <> $saltLength) {
    //global $local;
    //global $db_tiki;
    echo "Calculating systemSalt.  This may take several seconds.
    Type some characters, move your mouse to create more entropy,
    whatever that is...\n";
    $systemSalt_tiki = Installer::calc_system_salt();
    //echo "\$systemSalt_tiki=" . $systemSalt_tiki . "\n";
    $fw = fopen($local, 'a');  //append, not write! OK to keep a few; last one counts...
    $filetowrite = "\$systemSalt_tiki='" . $systemSalt_tiki . "';\n";
    fwrite($fw, $filetowrite);
    fclose($fw);
    //include ($local);
    //print_r('$systemSalt_tiki=' . $systemSalt_tiki . "\n");
}
else {
    echo "\$systemSalt_tiki already set, terminating.\n";

}

# Clear caches, since patches often manipulate the database directly without using the functions normally available outside the installer.
# All caches, even though scripts and patches surely don't affect them all.
# require_once 'lib/cache/cachelib.php';
# $cachelib->empty_cache();

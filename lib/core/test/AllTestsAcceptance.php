<?php

/******************************************************************
 * Use this file to run just a few tests that you care about, instead
 * of running AllTests.
 ******************************************************************/
  

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

/*
 * Load all the Tiki libraries, in case we need them for 
 * tests.
 * AD: Maybe it would be better if each test loaded those libraries it needs,
 * but loading Tiki libraries involves a lot of black magic, so it's best 
 * to do it once and do it centrally
 */
ini_set( 'include_path', ini_get('include_path') . ';.;../lib;../../..' );
loadTikiLibraries();

/*
 * Note: Need to reset the include pathes relative to the root of tiki, because 
 * inclusion of the tiki files, move the currrent directory
 * to the root.
 */
ini_set( 'include_path', ini_get('include_path') . ';./lib/core/test;./lib;.' );

require_once('AcceptanceTests/AllTests.php');

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HandpickedTests');

        $suite->addTest(AcceptanceTests_AllTests::suite());
        return $suite;
        
    }
}

function loadTikiLibraries() {
	/*
 	 * Note: for some reason, we need to declare many of the Tiki global variables
 	 * here, otherwise they end up being NULL
 	 */
	global $wikilib, $dbTiki, $smarty, $tikilib;
	require_once('tiki-setup.php');
	include_once('lib/wiki/wikilib.php');

	/*
 	 * Need to reset error reporting because it is changed by 
 	 * some of the tiki include files
 	 */
	ini_set( 'display_errors', 'on' );
	error_reporting( E_ALL );
	ini_set( 'include_path', ini_get('include_path') . ';.;../lib;../../..' );
}

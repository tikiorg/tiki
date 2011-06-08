<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

/* AD (2009-10-06): NOTE: Causes the tests to not work altogether. Comment it out for now.
ini_set( 'include_path', ini_get('include_path') . PATH_SEPARATOR . '.' . PATH_SEPARATOR . '../' . PATH_SEPARATOR . '../../..' );
loadTikiLibraries();
*/

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
 	 * here, otherwise they end up being NULL.
 	 *
 	 * Below, I declare those variables that seem most critical, and so far
 	 * my experiments work with just those 4. But we may need to define
 	 * more of them. 
 	 *
 	 * To find out all the global variables that are defined in a particular
 	 * tiki script, you can add the following statement at the bottom of the script:
 	 *
 	 *    print "<pre>-- upon exit, array_keys(\$GLOBAlS)="; var_dump(array_keys($GLOBALS)); print "</pre>\n";See a longer list of potentialOne way to find out global Tiki variables
 	 *
 	 * Note that most of those global variables will be system variables that have nothing
 	 * to do with Tiki, so we probably can ignore most of them.  
 	 */
	global $wikilib, $dbTiki, $smarty, $tikilib, $multilinguallib;
	require_once('tiki-setup.php');
	include_once('lib/wiki/wikilib.php');
	include_once('lib/multilingual/multilinguallib.php');
	require_once('lib/tikilib.php');
//	print "-- AllTestsAcceptance: \$multilinguallib="; var_dump($multilinguallib); print "\n";


	/*
 	 * Need to reset error reporting because it is changed by 
 	 * some of the tiki include files
 	 */
	ini_set( 'display_errors', 'on' );
	error_reporting( E_ALL );
	ini_set( 'include_path', ini_get('include_path') . PATH_SEPARATOR . '.' . PATH_SEPARATOR . '../../lib' . PATH_SEPARATOR . '../..' );

	/*
 	* Note: Need to reset the include pathes relative to the root of tiki, because 
 	* inclusion of the tiki files, move the currrent directory
 	* to the root.
 	*/
	ini_set( 'include_path', ini_get('include_path') . PATH_SEPARATOR . './lib/test' . PATH_SEPARATOR . './lib' . PATH_SEPARATOR . '.' );
}

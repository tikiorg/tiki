<?php

/******************************************************************
 * Use this file to run just a few tests that you care about, instead
 * of running AllTests.
 ******************************************************************/
  

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

ini_set( 'include_path', ini_get('include_path') . ';.;../lib;../../..' );

function tra( $string ) {
	return $string;
}

function __autoload( $name ) {
	$path = str_replace( '_', '/', $name );
	require_once( $path . '.php' );
}

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HandpickedTests');
        
//        $suite->addTest(AcceptanceTests_AllTests::suite());
//        $suite->addTestSuite('AcceptanceTests_MultilingualTest');
        $suite->addTestSuite('AcceptanceTests_ListPagesTest');
        $suite->addTestSuite('AcceptanceTests_SearchTest');
            
        return $suite;
        
    }
}

?>
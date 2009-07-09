<?php
ini_set( 'include_path', ini_get('include_path') . ';./lib/core/test;./lib/core/lib;.' );
print "-- AllTest: include ini_get('include_path') is: "; var_dump(ini_get('include_path')); print "\n";


//require_once('tiki-setup.php');			

runTikiSetupOUTSIDETestCase();

function runTikiSetupOUTSIDETestCase() {
	global $dbTiki;
	require_once('tiki-setup.php');			
}

/*
function runTikiSetupInsideTestCase() {
	ini_set( 'include_path', ini_get('include_path') . ';./lib/core/test');
	global $dbTiki;
	require_once('lib/core/test/TikiTestCase.php');
	$testCase = new TikiTestCase();
	$testCase->runTikiSetup();
}
*/

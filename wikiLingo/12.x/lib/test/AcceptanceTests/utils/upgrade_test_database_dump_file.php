<?php
/*
 * Created on Jun 24, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
set_include_path(get_include_path() . PATH_SEPARATOR . "../..");
set_include_path(get_include_path() . PATH_SEPARATOR . "../../..");
set_include_path(get_include_path() . PATH_SEPARATOR . "../../../..");

require_once 'TikiAcceptanceTestDBRestorer.php';

//die ("WARNING: This script will destroy the current Tiki db. Comment out this line in the script to proceed.");

if ($argc != 2) {
	die("Missing argument. USAGE: $argv[0] <dump_filename>");
}
 
$test_TikiAcceptanceTestDBRestorer = new TikiAcceptanceTestDBRestorerSQLDumps();
$test_TikiAcceptanceTestDBRestorer->restoreDB($argv[1]);

$local_php = 'db/local.php';

require_once('installer/installlib.php');

// Force autoloading
if (! class_exists('ADOConnection')) {
	die('AdoDb not found.');
}

include $local_php;
$dbTiki = ADONewConnection($db_tiki);
$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki);
$installer = new Installer;
$installer->update();

$test_TikiAcceptanceTestDBRestorer->create_dump_file($argv[1]);

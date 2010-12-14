<?php
/*
 * Created on Jun 22, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

set_include_path( get_include_path() .PATH_SEPARATOR . "../.." );
set_include_path( get_include_path() .PATH_SEPARATOR . "../../.." );
set_include_path( get_include_path() .PATH_SEPARATOR . "../../../.." );

require_once ('tiki-setup.php');
require_once('lib/test/TikiAcceptanceTestDBRestorer.php');

if ($argc != 2) {
	die("Missing argument. USAGE: $argv[0] <dump_filename>");
}
 
$test_TikiAcceptanceTestDBRestorer = new TikiAcceptanceTestDBRestorerSQLDumps();
$test_TikiAcceptanceTestDBRestorer->restoreDB($argv[1]);
echo "File DB was restored based on dump $argv[1]";


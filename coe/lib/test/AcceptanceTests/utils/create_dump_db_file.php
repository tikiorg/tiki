<?php
/*
 * Created on Jun 22, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once 'TikiAcceptanceTestDBRestorer.php';

if ($argc != 2) {
	die("Missing argument. USAGE: $argv[0] <dump_filename>");
}
 
$test_TikiAcceptanceTestDBRestorer = new TikiAcceptanceTestDBRestorer();
$test_TikiAcceptanceTestDBRestorer->create_dump_file($argv[1]);
echo "File $argv[1] was created in your mysql data directory";


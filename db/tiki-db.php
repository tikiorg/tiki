<?php
// Database connection for the tiki system
require_once('DB.php');
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki14b1';

if (file_exists(dirname(__FILE__) . '/tiki-db2.php'))
	require_once(dirname(__FILE__) . '/tiki-db2.php');

$dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = DB::connect($dsn);
if (DB::isError($dbTiki)) {        
  die ($dbTiki->getMessage());
} 

?>

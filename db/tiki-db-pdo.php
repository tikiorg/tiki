<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

// Set the host string for PDO dsn.
$db_hoststring = "host=$host_tiki";

if ($db_tiki == 'mysqli') {
		$db_tiki = 'mysql';

		// If using mysql and it is set to use sockets instead of hostname,
		// you can only use one method to connect, not both.  If $socket_tiki
		// is set in local.php, then it will override the hostname method
		// of connecting to the database.
		if (isset($socket_tiki)) {
			$db_hoststring = "unix_socket=$socket_tiki";
		}
}

if (isset($tiki_pdo_utf8) && $tiki_pdo_utf8) {
	$extra_params = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
} else {
	$extra_params = array();
}
	
try {
	//$dbTiki = new PDO("$db_tiki:host=$host_tiki;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
	$dbTiki = new PDO("$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki, $extra_params);
	$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
	$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

	require_once 'lib/core/lib/TikiDb/Pdo.php';
	TikiDb::set( new TikiDb_Pdo( $dbTiki ) );

} catch( PDOException $e ) {
	require_once 'setup_smarty.php';

	$smarty->assign( 'msg', $e->getMessage() );
	$smarty->assign( 'where', 'connection');
	echo $smarty->fetch( 'database-connection-error.tpl' );
	exit;
}

if( ! function_exists( 'close_connection' ) ) {
	function close_connection() {
		global $dbTiki;
		$dbTiki= NULL;
	}
}


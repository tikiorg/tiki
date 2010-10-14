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

try {
/*
	$pdoDriverOptions = empty( $client_charset )
		? array( PDO::MYSQL_ATTR_READ_DEFAULT_GROUP => true )
		: array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET $client_charset" );

	$dbTiki = new PDO("$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki, $pdoDriverOptions);
*/
	require_once 'lib/core/TikiDb/Pdo.php';

	$conn = false;
	$pdo_options = array();
	$pdo_post_queries = array();

	if( isset( $client_charset ) ) {
		$charset_query = "SET NAMES $client_charset";

		if ( defined('PDO::MYSQL_ATTR_INIT_COMMAND' ) ) {
			$pdo_options[ PDO::MYSQL_ATTR_INIT_COMMAND ] = $charset_query;
		} else {
			$pdo_post_queries[] = $charset_query;
		}

		unset( $charset_query );
	}

	$dbTiki = new PDO( "$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki, $pdo_options );
	$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
	$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);
	TikiDb::set( new TikiDb_Pdo( $dbTiki ) );

	$tempDb = TikiDb::get();
	if ( $api_tiki_forced || ( isset( $dbversion_tiki ) && $dbversion_tiki[0] >= 4 ) ) {
		$previousApi = $api_tiki;
	} else {
		$previousApi = 'adodb';
	}

	foreach( $pdo_post_queries as $query ) {
		$tempDb->query( $query );
	}

	unset( $tempDb, $pdo_options, $pdo_post_queries );

} catch( PDOException $e ) {
	require_once 'lib/init/smarty.php';

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


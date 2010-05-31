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
	require_once 'lib/core/lib/TikiDb/Pdo.php';

	$conn = false;
	if ( ! isset( $client_charset ) ) {
		// This case should not happen if Tiki was correctly updated with the web installer

		$dbTiki = new PDO( "$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki );
		$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
		$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
		$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);
		TikiDb::set( new TikiDb_Pdo( $dbTiki ) );

		$tempDb = TikiDb::get();
		$dbCharsetVariables = $tempDb->getCharsetVariables();
		$client_charset = $tempDb->detectBestClientCharset( $dbCharsetVariables );
		unset( $tempDb );

		$conn = ( ! empty($client_charset) && $client_charset == $dbCharsetVariables['character_set_client'] );
	}
	if ( empty( $client_charset ) ) {
		$client_charset = 'utf8';
	}

	if ( ! $conn ) {
	
		$charset_query = "SET CHARACTER SET $client_charset";
		if ( defined('PDO::MYSQL_ATTR_NIT_COMMAND' ) ) {
			$dbTiki = new PDO( "$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki,
				array( PDO::MYSQL_ATTR_INIT_COMMAND => $charset_query )
			);
		} else {
			// PHP 5.3.0 seems buggy and may need this query - see http://bugs.php.net/bug.php?id=47224
			//   but this method is just a workaround because it may not work if PHP tries to reconnect to the DB after loosing the connection
			//
			$dbTiki = new PDO( "$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki );
			$dbTiki->exec( $charset_query );
		}
		unset( $charset_query );

		$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
		$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
		$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

		TikiDb::set( new TikiDb_Pdo( $dbTiki ) );
	}

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


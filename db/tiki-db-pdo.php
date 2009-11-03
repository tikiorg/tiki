<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

// Set the host string for PDO dsn.
$db_hoststring = "host=$host_tiki";

switch ($db_tiki) {
	case 'postgres7':
	case 'postgres8':
		$db_tiki = 'pgsql';
		break;
	case 'mysqli':
		$db_tiki = 'mysql';

		// If using mysql and it is set to use sockets instead of hostname,
		// you can only use one method to connect, not both.  If $socket_tiki
		// is set in local.php, then it will override the hostname method
		// of connecting to the database.
		if (isset($socket_tiki)) {
			$db_hoststring = "unix_socket=$socket_tiki";
		}
		break;
	case 'oracle':
		$db_tiki = 'oci';
}

if ($db_tiki == 'sybase') {
	// avoid database change messages
	ini_set('sybct.min_server_severity', '11');
}

try {
	//$dbTiki = new PDO("$db_tiki:host=$host_tiki;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
	$dbTiki = new PDO("$db_tiki:$db_hoststring;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
	$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
	$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

	if ($db_tiki == 'sybase') {
		$dbTiki->exec('set quoted_identifier on');
	}

	require_once 'lib/core/lib/TikiDb/Pdo.php';
	TikiDb::set( new TikiDb_Pdo( $dbTiki ) );

} catch( PDOException $e ) {
	require_once 'setup_smarty.php';

	$smarty->assign( 'msg', $e->getMessage() );
	echo $smarty->fetch( 'database-connection-error.tpl' );
	exit;
}

if( ! function_exists( 'close_connection' ) ) {
	function close_connection() {
		global $dbTiki;
		$dbTiki= NULL;
	}
}


<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if( !isset( $local_php ) ) {
	$local_php = 'db/local.php';
}
if (is_file($local_php)) {
	require_once($local_php);
	if( $dbversion_tiki == '1.10' ) {
		$dbversion_tiki = '2.0';
	}
}

if (extension_loaded("pdo") and $api_tiki == 'pdo' ) {
	require_once('tikidblib-pdo.php');
} else {
	require_once('tikidblib-adodb.php');
}

<?php

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
	$separator = ';';
} else {
	$separator = ':';
}

ini_set( 'include_path', ini_get('include_path') . "{$separator}.{$separator}../lib{$separator}../../.." );

function tra( $string ) {
	return $string;
}

function __autoload( $name ) {
	$path = str_replace( '_', '/', $name );
	require_once( $path . '.php' );
}

$api_tiki = null;
require 'db/local.php';

if (extension_loaded("pdo") and $api_tiki == 'pdo' ) {
	require_once('db/tiki-db-pdo.php');
} else {
	require_once('db/tiki-db-adodb.php');
}

$db = TikiDb::get();
$db->setServerType( $db_tiki );


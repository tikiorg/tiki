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


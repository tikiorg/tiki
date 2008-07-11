<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.86.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function get_tables( $dbTiki )
{
	static $list = array();
	if( $list )
		return $list;

	$result = $dbTiki->Execute( "show tables" );
	while( $row = $result->fetchRow() )
		$list[] = reset( $row );

	return $list;
}

function get_pref( $dbTiki, $name, $default )
{
	$result = $dbTiki->query( "SELECT value FROM tiki_preferences WHERE name = ?", array( $name ) );
	if( $row = $result->fetchRow() )
		return reset( $row );
	
	return $default;
}

function user_is_admin( $dbTiki, $name )
{
	$result = $dbTiki->query( "SELECT COUNT(*) FROM users_users u INNER JOIN users_usergroups g ON u.userId = g.userId WHERE login = ?", array( $name ) );

	$count = reset( $result->fetchRow() );
	return $count > 0;
}

function installer_is_accessible()
{
	if( ! file_exists( 'db/local.php' ) )
		return true;

	include_once("lib/init/initlib.php");
	TikiInit::prependIncludePath('lib/adodb');
	TikiInit::prependIncludePath('lib/pear');

	include_once("db/local.php");
	include_once ('adodb.inc.php');

	$dbTiki = &ADONewConnection($db_tiki);
	if( ! $dbTiki->Connect( $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki ) )
		return false;

	$tables = get_tables( $dbTiki );

	if( ! in_array( 'users_users', $tables ) )
		return true;
	
	$session_type = get_pref( $dbTiki, 'session_db', 'n' );
	$cookie_name = 'tiki-user-' . get_pref( $dbTiki, 'cookie_name', 'tikiwiki' );

	if ($session_type == 'y') {
		include('db/local.php');
		$ADODB_SESSION_DRIVER=$db_tiki;
		$ADODB_SESSION_CONNECT=$host_tiki;
		$ADODB_SESSION_USER=$user_tiki;
		$ADODB_SESSION_PWD=$pass_tiki;
		$ADODB_SESSION_DB=$dbs_tiki;
		unset($db_tiki);
		unset($host_tiki);
		unset($user_tiki);
		unset($pass_tiki);
		unset($dbs_tiki);
		ini_set('session.save_handler','user');
		include('session/adodb-session.php');
	}

	session_start();

	if( ! isset( $_SESSION[$cookie_name] ) )
		return false;
	
	if( user_is_admin( $dbTiki, $_SESSION[$cookie_name] ) )
		return true;

	global $db_tiki;
	unset($db_tiki);

	return false;
}

if ( installer_is_accessible() ) {
	$logged = true;
	$admin_acc = 'y';
	include_once("installer/tiki-installer.php");
} else {
	header( 'Location: index.php' );
	exit;
}

?>

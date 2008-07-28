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

function installer_is_accessible()
{
	global $cookie_name, $dbTiki, $db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki;

	if( ! $dbTiki->Connect( $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki ) )
		return false;

	$tables = get_tables( $dbTiki );

	if( ! in_array( 'users_users', $tables ) )
		return true;
	
	$session_type = get_pref( $dbTiki, 'session_db', 'n' );
	$cookie_name = get_pref( $dbTiki, 'cookie_name', 'tikiwiki' );

	// Clean cookie name, the same way it's done in tiki-setup_base.php
	$cookie_name = 'tiki-user-'.ereg_replace("[^a-zA-Z0-9]", "", $cookie_name);

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
		include_once('lib/adodb/session/adodb-session.php');
	}

	session_start();

	if( ! isset( $_SESSION[$cookie_name] ) )
		return false;
	
	if( $_SESSION[$cookie_name] == 'admin' )
		return true;

	global $db_tiki;
	unset($db_tiki);

	return false;
}

$cookie_name = '';
if ( file_exists( 'db/local.php' ) ) {

	include_once("lib/init/initlib.php");
	require_once('lib/setup/tikisetup.class.php');
	TikiInit::prependIncludePath('lib/adodb');
	TikiInit::prependIncludePath('lib/pear');

	include('db/local.php');
	include_once('adodb.inc.php');
	$dbTiki = &ADONewConnection($db_tiki);

} else {
	$cookie_name = 'tiki-user-tikiwiki';
}

if ( $cookie_name != '' || installer_is_accessible() ) {
	$logged = true;
	$admin_acc = 'y';
	include_once("installer/tiki-installer.php");
} else {
	require_once("setup_smarty.php");
	require_once("lib/tikilib.php");
	include_once('lib/init/tra.php');
	$tikilib = new TikiLib($dbTiki);
	require_once("lib/userslib.php");
	$userlib = new UsersLib($dbTiki);
	require_once("lib/tikiaccesslib.php");
	$access = new TikiAccessLib();
	require_once('lib/setup/prefs.php');
	require_once('lib/setup/language.php');

	echo tra("<html><body>\n<h1>Security Alert!</h1>\n");
	echo tra('<p>The Tiki installer can be used only by a site adminstrator. Please login as an administrator first.</p>');
	$smarty->display('tiki-login.tpl');
	echo '</body></html>';
}

?>

<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if( ! isset( $_SERVER['argc'] ) )
	die( "Usage: php installer/shell.php <domain>\n" );
if( ! file_exists( 'db/local.php' ) )
	die( "Tiki is not installed yet.\n" );

if( isset( $_SERVER['argv'][1] ) && $_SERVER['argv'][1] != 'install' ) {
	$_SERVER['TIKI_VIRTUAL'] = basename( $_SERVER['argv'][1] );
}

require_once('lib/init/initlib.php');
require_once('lib/setup/tikisetup.class.php');
require_once('db/tiki-db.php');
require_once('installer/installlib.php');
include $local_php;

// In case of replication, ignore it during installer.
unset( $shadow_dbs, $shadow_user, $shadow_pass, $shadow_host );

class IgnoreErrorHandler implements TikiDb_ErrorHandler
{
	function handle( TikiDb $db, $query, $values, $result ) {
	}
}

TikiDb::get()->setErrorHandler( new IgnoreErrorHandler );

echo "Running installer for: $local_php\n";

$installer = new Installer;
if( $_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'install' )
	$installer->cleanInstall();
else {
	$installer->update();

	if( count( $installer->installed ) ) {
		echo "\tPatches installed:\n";
		foreach( $installer->installed as $patch )
			echo "\t\t$patch\n";
	}

	if( count( $installer->executed ) ) {
		echo "\tScripts executed:\n";
		foreach( $installer->executed as $script )
			echo "\t\t$script\n";
	}
	
	echo "\tQueries executed successfully: " . count($installer->success) . "\n";
	if( count( $installer->failures ) ) {
		echo "\tErrors:\n";
		foreach( $installer->failures as $key => $error ) {
			list( $query, $message ) = $error;

			echo "\t===== Error $key =====\n\t$query\n\t$message\n";
		}
	}
}

<?php

if( ! isset( $_SERVER['argc'] ) )
	die( "Usage: php installer/shell.php\n" );
if( ! file_exists( 'db/local.php' ) )
	die( "Tiki is not installed yet.\n" );

require_once('lib/init/initlib.php');
require_once('lib/setup/tikisetup.class.php');
TikiSetup::prependIncludePath($tikipath);
TikiSetup::prependIncludePath('lib');
TikiSetup::prependIncludePath('lib/pear');
require_once('tiki-setup_base.php');
require_once('installer/installlib.php');
include 'db/local.php';

$installer = new Installer;
if( $_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'install' )
	$installer->cleanInstall();
else {
	$installer->update();

	echo "Patches installed:\n";
	foreach( $installer->installed as $patch )
		echo "\t$patch\n";

	echo "Scripts executed:\n";
	foreach( $installer->executed as $script )
		echo "\t$script\n";
	
	echo "Queries executed successfully: " . count($installer->success) . "\n";
	echo "Errors:\n";
	foreach( $installer->failures as $key => $error ) {
		list( $query, $message ) = $error;

		echo "===== Error $key =====\n$query\n$message\n";
	}

	echo "\n==========\nPatches installed:\n";
	foreach( $installer->installed as $patch )
		echo "\t$patch\n";

	echo "\nScripts executed:\n";
	foreach( $installer->executed as $script )
		echo "\t$script\n";
	
	echo "\nQueries executed successfully: " . count($installer->success) . "\n";
}

?>

<?php
require_once 'tiki-setup.php';
require_once 'lib/TikiWebdav/autoload.php';
require_once 'lib/TikiWebdav/Server.php';
require_once 'lib/TikiWebdav/Backend/File.php';
require_once 'lib/TikiWebdav/PathFactories/File.php';
require_once 'lib/TikiWebdav/Auth/Default.php';

$server = TikiWebdav_Server::getInstance();
$pathFactory = new TikiWebdav_PathFactories_File;
$backend = new TikiWebdav_Backends_File;

$server->auth = new TikiWebdav_Auth_Default( '/tmp/tokens.php' ); ///FIXME
//$server->auth = new TikiWebdav_Auth_Default;
$server->pluginRegistry->registerPlugin(
	new ezcWebdavLockPluginConfiguration()
);

foreach ( $server->configurations as $conf ) {
	$conf->pathFactory = $pathFactory;
}

//@file_put_contents('/tmp/tiki4log', "\n=== handle() ===\n", FILE_APPEND );
global $filegallib; require_once('lib/filegals/filegallib.php');
$server->handle( $backend ); 

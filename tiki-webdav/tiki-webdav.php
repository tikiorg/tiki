<?php
require_once 'tiki-setup.php';

function error_handler($errno, $errmsg, $filename, $linenum, $vars) {
	@file_put_contents('/tmp/tiki4log', "\n=== ERROR ===\n", FILE_APPEND );
	@file_put_contents('/tmp/tiki4log', "$filename\n$linenum\n$errmsg\n", FILE_APPEND );
	@file_put_contents('/tmp/tiki4log', "\n===  BACTRACE ===\n", FILE_APPEND );
	@file_put_contents('/tmp/tiki4log', print_r(debug_backtrace(false),true), FILE_APPEND );
	@file_put_contents('/tmp/tiki4log', "\n===  BACTRACE END ===\n", FILE_APPEND );
	@file_put_contents('/tmp/tiki4log', "\n=== ERROR END ===\n", FILE_APPEND );
}
//$old_error_handler = set_error_handler("error_handler");

$access->check_feature('feature_webdav');

//@file_put_contents('/tmp/tiki4log', "\n=== _SERVER() ===\n".print_r($_SERVER,true)."\n", FILE_APPEND );
// Check if we come here with a browser
if ( $_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $_SERVER['SCRIPT_NAME'] ) {
	$smarty->assign('mid','tiki-webdav.tpl');
	$smarty->display("tiki.tpl");
} else {
require_once 'lib/TikiWebdav/autoload.php';
require_once 'lib/TikiWebdav/Server.php';
require_once 'lib/TikiWebdav/Backend/File.php';
require_once 'lib/TikiWebdav/PathFactories/File.php';
require_once 'lib/TikiWebdav/Auth/Default.php';

$server = TikiWebdav_Server::getInstance();
$pathFactory = new TikiWebdav_PathFactories_File;
$backend = new TikiWebdav_Backends_File;

//$server->auth = new TikiWebdav_Auth_Default( '/tmp/tokens.php' ); ///FIXME
//@file_put_contents('/tmp/tiki4log', "\n TOKENFile=". $backend->getRoot().'/.webdav-token.php'."\n", FILE_APPEND);
$server->auth = new TikiWebdav_Auth_Default ( $backend->getRoot().'/.webdav-token.php' );
$server->pluginRegistry->registerPlugin(
	new ezcWebdavLockPluginConfiguration()
);

foreach ( $server->configurations as $conf ) {
	$conf->pathFactory = $pathFactory;
}

//@file_put_contents('/tmp/tiki4log', "\n=== handle() ===\n", FILE_APPEND );
global $filegallib; require_once('lib/filegals/filegallib.php');
$server->handle( $backend ); 
//@file_put_contents('/tmp/tiki4log', "\n=== end handle() ===\n", FILE_APPEND );
}

<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';

$debug = false;
$debug_file= '/tmp/tiki4log';

function print_debug($string) {
	global $debug, $debug_file;
	
	if ( $debug !== false) {
		if (empty($debug_file)) {
			$debug_file = "/tmp/tiki.log";
		}
		@file_put_contents($debug_file, $string, FILE_APPEND );
	}
}

function error_handler($errno, $errmsg, $filename, $linenum, $vars) {
	print_debug("\n=== ERROR ===\n");
	print_debug("$filename\n$linenum\n$errmsg\n");
	print_debug("\n===  BACTRACE ===\n");
	print_debug(print_r(debug_backtrace(false),true));
	print_debug("\n===  BACTRACE END ===\n");
	print_debug("\n=== ERROR END ===\n");
}

if ($debug === true) {
	$old_error_handler = set_error_handler("error_handler");
}

$access->check_feature('feature_webdav');

print_debug("\n=== _SERVER() ===\n".print_r($_SERVER,true)."\n");
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

	print_debug("\n TOKENFile=". $backend->getRoot().'/.webdav-token.php'."\n");
	$server->auth = new TikiWebdav_Auth_Default ( $backend->getRoot().'/.webdav-token.php' );
	$server->pluginRegistry->registerPlugin(
			new ezcWebdavLockPluginConfiguration()
			);

	foreach ( $server->configurations as $conf ) {
		$conf->pathFactory = $pathFactory;
	}

	print_debug("\n=== handle() ===\n");
	global $filegallib; require_once('lib/filegals/filegallib.php');
	$server->handle( $backend ); 
	print_debug("\n=== end handle() ===\n");
}

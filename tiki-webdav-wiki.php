<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


// If Apache is calling PHP in CGI mode, authentication HTTP Headers are not set.
// In this case, you have to add the following lines inside your Apache VirtualHost config :
//   RewriteEngine on
//   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
//
// The two lines below are used to set PHP_AUTH_USER and PHP_AUTH_PW from HTTP_AUTHORIZATION to allow Basic Authentication in Tiki WebDAV

if ( !empty($_SERVER['HTTP_AUTHORIZATION']) && !isset($_SERVER['PHP_AUTH_USER']) ) {
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

$webdav_access = true;
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
	$smarty->assign('mid','tiki-webdav-wiki.tpl');
	$smarty->display("tiki.tpl");
} else {
	require_once 'lib/TikiWebdav/autoload.php';
	require_once 'lib/TikiWebdav/Server.php';
	require_once 'lib/TikiWebdav/Backend/Wiki.php';
	require_once 'lib/TikiWebdav/PathFactories/Wiki.php';
	require_once 'lib/TikiWebdav/Auth/Default.php';
	require_once 'lib/TikiWebdav/Auth/Wiki.php';

	$server = TikiWebdav_Server::getInstance();
	$server->options->realm = tra('Tiki WebDAV access');
	$pathFactory = new TikiWebdav_PathFactories_Wiki();
	$backend = new TikiWebdav_Backends_Wiki();

	print_debug("\n TOKENFile=". $backend->getRoot().'/.webdav-token.php'."\n");
	$server->auth = new TikiWebdav_Auth_Wiki( $backend->getRoot().'/.webdav-token.php' );

	foreach ( $server->configurations as $conf ) {
		$conf->pathFactory = $pathFactory;
	}

	print_debug("\n=== handle() ===\n");
	$server->handle( $backend );
	print_debug("\n=== end handle() ===\n");
}

<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


// If Apache is calling PHP in CGI mode, authentication HTTP Headers are not
// set.
// In this case, you have to add the following lines inside your Apache
// VirtualHost config :
//   RewriteEngine on
//           RewriteCond %{HTTP:Authorization} ^(.*)
//           RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
//
// The two lines below are used to set PHP_AUTH_USER and PHP_AUTH_PW from
// HTTP_AUTHORIZATION to allow Basic Authentication in Tiki WebDAV

if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
	$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
	$ha = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
	$parts = explode(':', $ha);
	while (count($parts) < 2) {
		$parts[] = null;
	}
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = $parts;
}

$webdav_access = false;
require_once 'tiki-setup.php';

$debug = false;
$debug_file= '/tmp/tiki4log';

/**
 * @param $string
 */
function print_debug($string)
{
	global $debug, $debug_file;
	
	if ( $debug !== false) {
		if (empty($debug_file)) {
			$debug_file = "/tmp/tiki.log";
		}
		@file_put_contents($debug_file, $string, FILE_APPEND);
	}
}

/**
 * @param $errno
 * @param $errmsg
 * @param $filename
 * @param $linenum
 * @param $vars
 */
function error_handler($errno, $errmsg, $filename, $linenum, $vars)
{
	print_debug("\n=== ERROR ===\n");
	print_debug("$filename\n$linenum\n$errmsg\n");
	print_debug("\n===  BACTRACE ===\n");
	print_debug(print_r(debug_backtrace(false), true));
	print_debug("\n===  BACTRACE END ===\n");
	print_debug("\n=== ERROR END ===\n");
}

if ($debug === true) {
	$old_error_handler = set_error_handler("error_handler");
}

$access->check_feature('feature_webdav');

// Check if we come here with a browser
if ( $_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $_SERVER['SCRIPT_NAME'] ) {
	$smarty->assign('mid', 'tiki-webdav.tpl');
	$smarty->display("tiki.tpl");
} else {
	if ( empty($_SERVER['PATH_INFO']) ) {
		$_SERVER['PATH_INFO'] = '/';
	}

	$path = preg_replace('#.*tiki-webdav\.php#', '', rawurldecode(trim($_SERVER['REQUEST_URI'])));
	$path = rtrim($path, '?');

	print_debug("\n=== _SERVER() ===\n".print_r($_SERVER, true)."\n");
	$server = TikiWebdav_Server::getInstance();
	$server->options->realm = tra('Tiki WebDAV access');

	print_debug("\n====PATH : $path ====\n");
	if (preg_match('/^\/Wiki Pages\//', $path)) {
		print_debug("\n====Wiki====\n");

		$pathFactory = new TikiWebdav_PathFactories_Wiki();
		$backend = new TikiWebdav_Backends_Wiki();

		$path = preg_replace('/^\/Wiki Pages\//', '', $path);
		if (empty($path)) {
			$path = '/';
		}
		print_debug("\n====PATH : $path ====\n");
		print_debug("\n TOKENFile=". $backend->getRoot().'/.webdav-token.php'."\n");
		$server->auth = new TikiWebdav_Auth_Wiki($backend->getRoot().'/.webdav-token.php');

	} else {

		$pathFactory = new TikiWebdav_PathFactories_File;
		$backend = new TikiWebdav_Backends_File;

		print_debug("\n TOKENFile=". $backend->getRoot().'/.webdav-token.php'."\n");
		$server->auth = new TikiWebdav_Auth_Default($backend->getRoot().'/.webdav-token.php');
		$server->pluginRegistry->registerPlugin(new ezcWebdavLockPluginConfiguration());
	}

	foreach ( $server->configurations as $conf ) {
		$conf->pathFactory = $pathFactory;
	}

	print_debug("\n=== handle() ===\n");
	$filegallib = TikiLib::lib('filegal');
	$server->handle($backend, $path); 
	print_debug("\n=== end handle() ===\n");
}

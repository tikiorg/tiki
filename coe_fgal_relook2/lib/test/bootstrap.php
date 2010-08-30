<?php

define( 'CUSTOM_ERROR_LEVEL', defined( 'E_DEPRECATED' ) ? E_ALL ^ E_DEPRECATED : E_ALL );

require_once(dirname(__FILE__) . '/TikiTestCase.php');

ini_set( 'display_errors', 'on' );
error_reporting( CUSTOM_ERROR_LEVEL );

$paths = array(
	ini_get('include_path'),
	realpath('.'),
	realpath('../core'),
	realpath('../..'),
	realpath('core'),
	realpath('../pear'),
);

ini_set( 'include_path', implode( PATH_SEPARATOR, $paths ) );

function __autoload( $name ) {
	$path = str_replace( '_', '/', $name ) . '.php';
	@ include_once( $path );
}

$tikidomain = '';
$api_tiki = null;
require 'db/local.php';

if (extension_loaded("pdo") and $api_tiki == 'pdo' ) {
	require_once('db/tiki-db-pdo.php');
} else {
	require_once('db/tiki-db-adodb.php');
}

$db = TikiDb::get();
$db->setServerType( $db_tiki );

$pwd = getcwd();
chdir( dirname(__FILE__) . '/../..' );
require_once 'lib/init/smarty.php';
require_once 'lib/cache/cachelib.php';
require_once 'lib/tikilib.php';
require_once 'lib/userslib.php';
require_once 'lib/headerlib.php';
require_once 'lib/init/tra.php';

global $tikilib;
$tikilib = new TikiLib;
$userlib = new UsersLib;
$_SESSION = array(
	'u_info' => array(
		'login' => null
	)
);
chdir($pwd);
global $user_overrider_prefs;
$user_overrider_prefs = array();
require_once 'lib/setup/prefs.php';

ini_set( 'display_errors', 'on' );
error_reporting( CUSTOM_ERROR_LEVEL );

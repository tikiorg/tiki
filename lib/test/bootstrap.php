<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

define('TIKI_IN_TEST', 1);
define('TIKI_PATH', realpath(__DIR__ . '/../../'));
define('CUSTOM_ERROR_LEVEL', defined('E_DEPRECATED') ? E_ALL ^ E_DEPRECATED : E_ALL);

ini_set('display_errors', 'on');
error_reporting(CUSTOM_ERROR_LEVEL);

$paths = array(
		ini_get('include_path'),
		realpath('.'),
		realpath('../core'),
		realpath('../..'),
		realpath('core'),
		realpath('../pear'),
        realpath('../../vendor'),
        realpath('../../vendor/mikey179/vfsStream/src/main/php'),
        realpath('../../vendor_extra/pear')
		);

ini_set('include_path', implode(PATH_SEPARATOR, $paths));

require_once __DIR__ . '/../../vendor/autoload.php';

if (!is_file(dirname(__FILE__) . '/local.php')) {
	die("\nYou need to setup a new database and create a local.php file for the test suite inside " . dirname(__FILE__) . "\n\n");
}

global $local_php, $api_tiki, $style_base;
$api_tiki = 'adodb';
$local_php = __DIR__ . '/local.php';
require_once($local_php);

$style_base = 'skeleton';

// Force autoloading
if (! class_exists('ADOConnection')) {
	die('AdoDb not found.');
}

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$initializer = new TikiDb_Initializer;
$initializer->setPreferredConnector($api_tiki);
$db = $initializer->getConnection(
	array(
		'host' => $host_tiki,
		'user' => $user_tiki,
		'pass' => $pass_tiki,
		'dbs' => $dbs_tiki,
		'charset' => $client_charset,
	)
);

if (! $db) {
	die("\nUnable to connect to the database\n\n");
}

TikiDb::set($db);

global $tikilib;
require_once 'lib/tikilib.php';
$tikilib = new TikiLib;

// update db if needed
require_once 'lib/init/initlib.php';
include_once ('installer/installlib.php');
$installer = new Installer;

if (!$installer->tableExists('tiki_preferences')) {
	echo "Installing Tiki database...\n";
	$installer->cleanInstall();
} else if ($installer->requiresUpdate()) {
	echo "Updating Tiki database...\n";
	$installer->update();
}

$pwd = getcwd();
chdir(dirname(__FILE__) . '/../..');
$smarty = TikiLib::lib('smarty');
require_once 'lib/init/smarty.php';
$smarty->addPluginsDir('../smarty_tiki/');
$cachelib = TikiLib::lib('cache');
$wikilib = TikiLib::lib('wiki');
$userlib = TikiLib::lib('user');
$headerlib = TikiLib::lib('header');
require_once 'lib/init/tra.php';
$access = TikiLib::lib('access');

$_SESSION = array(
		'u_info' => array(
			'login' => null
			)
		);
chdir($pwd);

require_once(dirname(__FILE__) . '/TikiTestCase.php');
require_once(dirname(__FILE__) . '/TestableTikiLib.php');

global $systemConfiguration;
$systemConfiguration = new Zend\Config\Config(
	array(
		'preference' => array(),
		'rules' => array(),
	),
	array('readOnly' => false)
);

global $user_overrider_prefs, $prefs;
$user_overrider_prefs = array();
$prefs['language'] = 'en';
$prefs['site_language'] = 'en';
require_once 'lib/setup/prefs.php';

ini_set('display_errors', 'on');
error_reporting(CUSTOM_ERROR_LEVEL);

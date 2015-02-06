<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

define('TIKI_IN_TEST', 1);
require_once(dirname(__FILE__) . '/../TikiTestCase.php');

ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_DEPRECATED);

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "." . PATH_SEPARATOR . "../../core" . PATH_SEPARATOR . "../../..");
include_once('./include_non_autoload_compatible_classes.php');

function tra($string)
{
	return $string;
}

require __DIR__ . '/../../../vendor/autoload.php';

$tikidomain = '';
$api_tiki = null;
require 'db/local.php';

if (extension_loaded("pdo") and $api_tiki == 'pdo') {
	require_once('db/tiki-db-pdo.php');
} else {
	require_once('db/tiki-db-adodb.php');
}

$db = TikiDb::get();
$db->setServerType($db_tiki);

$pwd = getcwd();
chdir(dirname(__FILE__) . '/../../../');
$cachelib = TikiLib::lib('cache');
chdir($pwd);

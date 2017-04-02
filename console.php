#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Console\Input\ArgvInput;

define('TIKI_CONSOLE', 1);
declare(ticks = 1); // how often to check for signals

if (function_exists('pcntl_signal')) {
	$exit = function () {
		error_reporting(0); // Disable error reporting, misleading backtrace on kill
		exit;
	};

	pcntl_signal(SIGTERM, $exit);
	pcntl_signal(SIGHUP, $exit);
	pcntl_signal(SIGINT, $exit);
}


if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

require_once 'tiki-filter-base.php';
require_once 'lib/init/initlib.php';
include_once 'lib/init/tra.php';
require_once 'lib/setup/tikisetup.class.php';
require_once 'lib/setup/twversion.class.php';

$input = new ArgvInput;

if (false !== $site = $input->getParameterOption(array('--site'))) {
	$_SERVER['TIKI_VIRTUAL'] = $site;
}

$local_php = TikiInit::getCredentialsFile();

if (! is_readable($local_php)) {
	die("Credentials file local.php not found. See http://doc.tiki.org/Installation for more information.\n");
}

if (is_file($local_php) || TikiInit::getEnvironmentCredentials()) {
	require_once 'db/tiki-db.php';
}

$installer = $installer = new Installer;
$isInstalled = $installer->isInstalled();

$exceptionToRender = null;
if ($isInstalled) {
	$bypass_siteclose_check = true;
	try {
		require_once 'tiki-setup.php';
	} catch (Exception $e) {
		$exceptionToRender = $e;
	}

	if (! $asUser = $input->getParameterOption(array('--as-user'))) {
		$asUser = 'admin';
	}

	if (TikiLib::lib('user')->user_exists($asUser)) {
		$permissionContext = new Perms_Context($asUser);
	}
}

$consoleBuilder = new Tiki\Command\ConsoleApplicationBuilder($site);
$console = $consoleBuilder->create();

if ($exceptionToRender instanceof Exception){
	$console->renderException($exceptionToRender, new \Symfony\Component\Console\Output\ConsoleOutput());
}

$console->run();

<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Console\Input\ArgvInput;

if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

require_once 'tiki-filter-base.php';
require_once 'lib/init/initlib.php';
include_once('lib/init/tra.php');
require_once('lib/setup/tikisetup.class.php');
require_once 'lib/setup/twversion.class.php';

$input = new ArgvInput;

if (false !== $site = $input->getParameterOption(array('--site'))) {
	$_SERVER['TIKI_VIRTUAL'] = $site;
}

$local_php = TikiInit::getCredentialsFile();

$console = new Tiki\Command\Application;

$console->add(new Tiki\Command\ConfigureCommand);
if (is_file($local_php)) {
	require 'db/tiki-db.php';
	$console->add(new Tiki\Command\InstallCommand);
	$console->add(new Tiki\Command\UpdateCommand);

	$installer = new Installer;
	if (! $installer->requiresUpdate()) {
		require 'tiki-setup.php';
		$console->add(new Tiki\Command\CacheClearCommand);
	}
}

$console->run();

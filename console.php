#!/usr/bin/php
<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
if (is_file($local_php) || TikiInit::getEnvironmentCredentials()) {
	require 'db/tiki-db.php';
	$console->add(new Tiki\Command\InstallCommand);
	$console->add(new Tiki\Command\UpdateCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('database:install'));
	$console->add(new Tiki\Command\UnavailableCommand('database:update'));
}

$installer = $installer = new Installer;
$isInstalled = $installer->isInstalled();

if ($isInstalled) {
	$bypass_siteclose_check = true;
	require_once 'tiki-setup.php';

	if (! $asUser = $input->getParameterOption(array('--as-user'))) {
		$asUser = 'admin';
	}

	if (TikiLib::lib('user')->user_exists($asUser)) {
		$permissionContext = new Perms_Context($asUser);
	}
}

if ($isInstalled) {
	$console->add(new Tiki\Command\CacheClearCommand);
	$console->add(new Tiki\Command\BackupDBCommand);
	$console->add(new Tiki\Command\BackupFilesCommand);
	$console->add(new Tiki\Command\ProfileBaselineCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('cache:clear'));
	$console->add(new Tiki\Command\UnavailableCommand('database:backup'));
	$console->add(new Tiki\Command\UnavailableCommand('backup:files'));
	$console->add(new Tiki\Command\UnavailableCommand('profile:baseline'));
}

if ($isInstalled && ! $installer->requiresUpdate()) {
    $console->add(new Tiki\Command\DailyReportSendCommand);
	$console->add(new Tiki\Command\GoalCheckCommand);
	$console->add(new Tiki\Command\IndexRebuildCommand);
	$console->add(new Tiki\Command\IndexOptimizeCommand);
	$console->add(new Tiki\Command\IndexCatchUpCommand);
	$console->add(new Tiki\Command\MailInPollCommand);
    $console->add(new Tiki\Command\MailQueueSendCommand);
	$console->add(new Tiki\Command\NotificationDigestCommand);
	$console->add(new Tiki\Command\ProfileForgetCommand);
	$console->add(new Tiki\Command\ProfileInstallCommand);
	$console->add(new Tiki\Command\ProfileExport\Init);
	$console->add(new Tiki\Command\RefreshRssCommand);
} else {
    $console->add(new Tiki\Command\UnavailableCommand('daily-report:send'));
	$console->add(new Tiki\Command\UnavailableCommand('goal:check'));
	$console->add(new Tiki\Command\UnavailableCommand('index:rebuild'));
	$console->add(new Tiki\Command\UnavailableCommand('index:optimize'));
	$console->add(new Tiki\Command\UnavailableCommand('index:catch-up'));
	$console->add(new Tiki\Command\UnavailableCommand('mail-in:poll'));
	$console->add(new Tiki\Command\UnavailableCommand('mail-queue:send'));
	$console->add(new Tiki\Command\UnavailableCommand('notification:digest'));
	$console->add(new Tiki\Command\UnavailableCommand('profile:forget'));
	$console->add(new Tiki\Command\UnavailableCommand('profile:apply'));
	$console->add(new Tiki\Command\UnavailableCommand('profile:export:init'));
	$console->add(new Tiki\Command\UnavailableCommand('rss:refresh'));
}

if (file_exists('profiles/info.ini')) {
	$console->add(new Tiki\Command\ProfileExport\ActivityRuleSet);
	$console->add(new Tiki\Command\ProfileExport\ActivityStreamRule);
	$console->add(new Tiki\Command\ProfileExport\Article);
	$console->add(new Tiki\Command\ProfileExport\ArticleTopic);
	$console->add(new Tiki\Command\ProfileExport\ArticleType);
	$console->add(new Tiki\Command\ProfileExport\AllModules);
	$console->add(new Tiki\Command\ProfileExport\Category);
	$console->add(new Tiki\Command\ProfileExport\FileGallery);
	$console->add(new Tiki\Command\ProfileExport\Forum);
	$console->add(new Tiki\Command\ProfileExport\Goal);
	$console->add(new Tiki\Command\ProfileExport\GoalSet);
	$console->add(new Tiki\Command\ProfileExport\Group);
	$console->add(new Tiki\Command\ProfileExport\IncludeProfile);
	$console->add(new Tiki\Command\ProfileExport\Menu);
	$console->add(new Tiki\Command\ProfileExport\Module);
	$console->add(new Tiki\Command\ProfileExport\Preference);
	$console->add(new Tiki\Command\ProfileExport\RatingConfig);
	$console->add(new Tiki\Command\ProfileExport\RatingConfigSet);
	$console->add(new Tiki\Command\ProfileExport\RecentChanges);
	$console->add(new Tiki\Command\ProfileExport\Rss);
	$console->add(new Tiki\Command\ProfileExport\Tracker);
	$console->add(new Tiki\Command\ProfileExport\TrackerField);
	$console->add(new Tiki\Command\ProfileExport\WikiPage);

	$console->add(new Tiki\Command\ProfileExport\Finalize);
}

if (is_file('db/redact/local.php') && ($site == 'redact') ) {
	$console->add(new Tiki\Command\RedactDBCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('database:redact'));
}

$console->run();

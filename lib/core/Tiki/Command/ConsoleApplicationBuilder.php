<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Command;

use Installer;
use TikiInit;

/**
 * Builds a console application
 *
 * - listOfRegisteredConsoleCommands: allows to register commands
 * - create: creates the console application
 *
 * @package Tiki\Command
 */
class ConsoleApplicationBuilder
{
	const ACTION_NOT_AVAILABLE = 'not-available';
	const ACTION_NOT_PUBLISHED = 'not-published';

	protected $site; // the virtual site
	protected $baseDir;
	static protected $lastInstance;

	/**
	 * List of commands registered on the console
	 *
	 * When you need to register a new command, just add it to the right group, or create a new group if
	 * you need to test a new/different condition to register / not register a command.
	 *
	 * There are two behaviors when the check function returns false:
	 * - ACTION_NOT_AVAILABLE: register the command as not being available
	 * - ACTION_NOT_PUBLISHED: skip the command and do not register the command at all
	 *
	 * @return array the list of commands, grouped by test
	 */
	protected function listOfRegisteredConsoleCommands()
	{
		return [
			'checkTrue' => [
				'action' => self::ACTION_NOT_AVAILABLE,
				'commands' => [
					new ConfigureCommand,
				],
			],
			'checkConfigurationIsAvailable' => [
				'action' => self::ACTION_NOT_AVAILABLE,
				'commands' => [
					new InstallCommand,
					new UpdateCommand,
					new MultiTikiListCommand,
					new MultiTikiMoveCommand,
				],
			],
			'checkIsInstalled' => [
				'action' => self::ACTION_NOT_AVAILABLE,
				'commands' => [
					new CacheClearCommand,
					new LessCompileCommand,
					new BackupDBCommand,
					new BackupFilesCommand,
					new ProfileBaselineCommand,
					new InstallerLockCommand,
					new PatchCommand
				],
			],
			'checkIsInstalledAndDoNotRequireUpdate' => [
				'action' => self::ACTION_NOT_AVAILABLE,
				'commands' => [
					new AddonInstallCommand,
					new AddonRemoveCommand,
					new AddonUpgradeCommand,
					new DailyReportSendCommand,
					new GoalCheckCommand,
					new FilesBatchuploadCommand,
					new FilesDeleteoldCommand,
					new IndexRebuildCommand,
					new IndexOptimizeCommand,
					new IndexCatchUpCommand,
					new ListExecuteCommand,
					new MailInPollCommand,
					new MailQueueSendCommand,
					new NotificationDigestCommand,
					new PreferencesGetCommand,
					new PreferencesSetCommand,
					new PreferencesDeleteCommand,
					new ProfileForgetCommand,
					new ProfileInstallCommand,
					new ProfileExport\Init,
					new RecommendationBatchCommand,
					new RefreshRssCommand,
					new RssClearCacheCommand,
					new SchedulerRunCommand,
					new TrackerImportCommand,
					new TrackerClearCommand,
					new AdminIndexRebuildCommand,
					new UsersListCommand,
					new UsersPasswordCommand,
				],
			],
			'checkProfileInfoExists' => [
				'action' => self::ACTION_NOT_PUBLISHED,
				'commands' => [
					new ProfileExport\ActivityRuleSet,
					new ProfileExport\ActivityStreamRule,
					new ProfileExport\Article,
					new ProfileExport\ArticleTopic,
					new ProfileExport\ArticleType,
					new ProfileExport\AllModules,
					new ProfileExport\Calendar,
					new ProfileExport\Category,
					new ProfileExport\FileGallery,
					new ProfileExport\Forum,
					new ProfileExport\Goal,
					new ProfileExport\GoalSet,
					new ProfileExport\Group,
					new ProfileExport\IncludeProfile,
					new ProfileExport\Menu,
					new ProfileExport\Module,
					new ProfileExport\Preference,
					new ProfileExport\RatingConfig,
					new ProfileExport\RatingConfigSet,
					new ProfileExport\RecentChanges,
					new ProfileExport\Rss,
					new ProfileExport\Tracker,
					new ProfileExport\TrackerField,
					new ProfileExport\WikiPage,
					new ProfileExport\Finalize,
				],
			],
			'checkForLocalRedactDb' => [
				'action' => self::ACTION_NOT_AVAILABLE,
				'commands' => [
					new RedactDBCommand,
				],
			],
		];
	}

	/**
	 * ConsoleApplicationBuilder constructor.
	 * @param string $site Tiki virtual site (if available)
	 */
	public function __construct($site = "")
	{
		$this->site = $site;
		$this->baseDir = realpath(__DIR__ . '/../../../../'); // tiki root folder
	}

	/**
	 * Dummy Check that always returns true (for commands that we always want to register)
	 * @return bool
	 */
	protected function checkTrue()
	{
		return true;
	}

	/**
	 * Check if db configuration is available
	 * @return bool
	 */
	protected function checkConfigurationIsAvailable()
	{
		$local_php = TikiInit::getCredentialsFile();
		$result = (is_file($local_php) || TikiInit::getEnvironmentCredentials()) ? true : false;

		return $result;
	}

	/**
	 * Check if app reports as being fully installed
	 * @return bool
	 */
	protected function checkIsInstalled()
	{
		$installer = new Installer;
		$result = $installer->isInstalled() ? true : false;

		return $result;
	}

	/**
	 * Check if app reports as being fully installed, and db doesn't require updates
	 * @return bool
	 */
	protected function checkIsInstalledAndDoNotRequireUpdate()
	{
		$installer = new Installer;
		$result = ($installer->isInstalled() && !$installer->requiresUpdate()) ? true : false;

		return $result;
	}

	/**
	 * Check if the profile info.ini file exists
	 * @return bool
	 */
	protected function checkProfileInfoExists()
	{
		$result = file_exists($this->baseDir . '/profiles/info.ini') ? true : false;

		return $result;
	}

	/**
	 * Checks if the db configuration for redact exists and a "redact" vhost is being used.
	 * @return bool
	 */
	protected function checkForLocalRedactDb()
	{
		$result = (is_file($this->baseDir . '/db/redact/local.php') && ($this->site == 'redact')) ? true : false;

		return $result;
	}

	/**
	 * Creates a console application
	 *
	 * Iterates over all commands in the list, and registers / doesn't register the commands in accordance with the result
	 * of the check function and the action configured for the command group
	 *
	 * @param boolean $returnLastInstance
	 * @return Application
	 */
	public function create($returnLastInstance = false)
	{
		if ($returnLastInstance && self::$lastInstance instanceof self){
			return self::$lastInstance;
		}

		$console = new Application;

		foreach ($this->listOfRegisteredConsoleCommands() as $condition => $CommandGroupDefinition) {

			$available = call_user_func(array($this, $condition));
			$actionWhenNotAvailable = $CommandGroupDefinition['action'];

			/** @var \Symfony\Component\Console\Command\Command $command */
			foreach ($CommandGroupDefinition['commands'] as $command) {
				if ($available) {
					$console->add($command);
				} else {
					if ($actionWhenNotAvailable === self::ACTION_NOT_AVAILABLE) {
						$console->add(new UnavailableCommand($command->getName()))->ignoreValidationErrors();
					}
				}
			}

		}

		self::$lastInstance = $console;

		return $console;
	}

}

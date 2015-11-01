<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Util\FileSystem;
use Composer\EventDispatcher\EventSubscriberInterface;

class ThemeInstaller extends LibraryInstaller implements EventSubscriberInterface
{
	private $queue = [];

	public static function setup(Event $event)
	{
		$composer = $event->getComposer();

		$installer = new self($event->getIO(), $composer, 'tiki-theme');

		$composer->getInstallationManager()
			->addInstaller($installer);
		$composer->getEventDispatcher()
			->addSubscriber($installer);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPackageBasePath(PackageInterface $package)
	{
		$themes = __DIR__ . '/../../../../themes/';

		$prefix = $package->getPrettyName();
		$prefix = preg_replace('/[^\w]+/', '_', $prefix);

		return $themes . $prefix;
	}

	/**
	 * {@inheritDoc}
	 */
	public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
	{
		parent::install($repo, $package);

		$this->queue[] = $package;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
	{
		parent::update($repo, $initial, $target);

		$this->queue[] = $package;
	}

	public function finalize()
	{
		require_once 'vendor/autoload.php';
		foreach ($this->queue as $package) {
			$this->finalizePackage($package);
		}
	}

	private function finalizePackage($package)
	{
		$fs = new FileSystem;
		$base = $this->getPackageBasePath($package);
		$fs->ensureDirectoryExists("$base/css");

		$compiler = new \lessc;
		$compiler->compileFile("$base/less/tiki.less", "$base/css/$base.css");

		// Clean-up undesired files
		$fs->remove("$base/dist");
		$fs->remove("$base/docs");
		$fs->remove("$base/grunt");
		$fs->remove("$base/js");
		$fs->remove("$base/test-infra");
	}

	public static function getSubscribedEvents()
	{
		return [
			ScriptEvents::POST_INSTALL_CMD => 'finalize',
			ScriptEvents::POST_UPDATE_CMD => 'finalize',
		];
	}
}


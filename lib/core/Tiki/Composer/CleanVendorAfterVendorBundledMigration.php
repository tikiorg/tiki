<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;

use Composer\Script\Event;
use Composer\Util\FileSystem;

/**
 * After Migrate the vendors to vendors_bundled, we should clean the vendor folder
 * We don't want to that by deleting all files in the vendor folder, instead we will try
 * to do sensitive decisions about what to delete
 *
 * All the process is skipped exists a file called "do_not_clean.txt" in the vendor folder
 *
 * Class CleanVendorAfterVendorBundledMigration
 * @package Tiki\Composer
 */
class CleanVendorAfterVendorBundledMigration
{
	/**
	 * @param Event $event
	 */
	public static function clean(Event $event)
	{

		/*
		 * 0) Make sure old bin links are removed so they can be created by composer
		 * 1) If a file called do_not_clean.txt exists in the vendor folder stop
		 * 2) If there is a composer.json file, warn the user that they might need to clean the folder by themselves
		 * 3) If there is no composer.json in the root, clean all folders and autoload.php in the vendor folder
		 */

		$io = $event->getIO();
		$fs = new FileSystem();

		$rootFolder = realpath(__DIR__.'/../../../../');
		$oldVendorFolder = realpath($rootFolder.'/vendor');

		// 0) Make sure we can install known bin files (they might be still linked to the old vendor folder
		$binFiles = ['lessc', 'minifycss', 'minifyjs', 'dbunit', 'phpunit'];

		foreach ($binFiles as $file) {
			$filePath = $rootFolder.'/bin/'.$file;
			if (is_link($filePath)) {
				$linkDestination = readlink($filePath);
				$fileRealPath = realpath($filePath);
				if ( strncmp($linkDestination, '../vendor/', strlen('../vendor/')) === 0 // relative link to vendor folder
					|| $filePath === false // target don't exists, so link is broken
					|| strncmp($fileRealPath, $oldVendorFolder, strlen($oldVendorFolder)) === 0 // still pointing to old vendor folder
				) {
					$fs->unlink($filePath);
				}
			}
		}

		// if we cant find the vendor dir no sense in progressing
		if ($oldVendorFolder === false || !is_dir($oldVendorFolder)) {
			return;
		}

		// 1) If a file called do_not_clean.txt exists in the vendor folder stop
		if (file_exists($oldVendorFolder.'/do_not_clean.txt')) {
			$io->write('');
			$io->write('File vendor/do_not_clean.txt is present, no attempt to clean the vendor folder will be done!');
			$io->write('');

			return;
		}

		// 2) If there is a composer.json file, warn the user that they might need to clean the folder themselves
		if (file_exists($rootFolder.'/composer.json')) {
			$io->write('');
			$io->write(
				'Since the is a composer.json file in the root of the site, we will not try to clean your vendor folder'
			);
			$io->write('as part of the migration from vendor to vendor_bundled/vendor, you need to review that yourself!');
			$io->write('');

			return;
		}

		// 3) If there is no composer.json in the root, clean all folders and autoload.php in the vendor folder

		$fs->remove($oldVendorFolder.'/autoload.php');

		$vendorDirsCleaned = false;
		$vendorDirs = glob($oldVendorFolder.'/*', GLOB_ONLYDIR);
		foreach ($vendorDirs as $dir) {
			if (is_dir($dir)) {
				$fs->remove($dir);
				$vendorDirsCleaned = true;
			}
		}

		if ($vendorDirsCleaned){
			// there are some cached templates that will stop tiki to work after the migration
			$loopDirs = array_merge([$rootFolder . '/temp/templates_c'], glob($rootFolder . '/temp/templates_c/*',GLOB_ONLYDIR));
			foreach($loopDirs as $dir){
				$cachedTemplates = glob($dir . '/*.tpl.php');
				foreach($cachedTemplates as $template){
					$fs->remove($template);
				}
			}
		}
	}
}


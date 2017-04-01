<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;

use Composer\Script\Event;
use Composer\Util\FileSystem;
use Symfony\Component\Finder\Finder;

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

	// To calculate the md5 hash for the old vendor folder, on a linux server, you can use (inside the old vendor folder):
	//
	// $ STRING=$(ls -d */* | grep -v "^composer" | sort -f | tr '\n' ':' | sed 's/:$//')
	// $ echo -n $STRING | md5
	//
	const PRE_MIGRATION_OLD_VENDOR_FOLDER_MD5_HASH = '6997e3dc0e3ad453ab8ea9798653a0fa';

	/**
	 * @param Event $event
	 */
	public static function clean(Event $event)
	{

		/*
		 * 0) Make sure old bin links are removed so they can be created by composer
		 * 1) If a file called do_not_clean.txt exists in the vendor folder stop
		 * 2) If there is a vendor/autoload.php, check the hash of the folder structure, if different from at the time of the vendor_bundle migration, ignore
		 * 3) If we arrive here, clean all folders and autoload.php in the old (pre migration) vendor folder
		 */

		$io = $event->getIO();
		$fs = new FileSystem();

		$rootFolder = realpath(__DIR__ . '/../../../../');
		$oldVendorFolder = realpath($rootFolder . '/vendor');

		// 0) Make sure we can install known bin files (they might be still linked to the old vendor folder
		$binFiles = ['lessc', 'minifycss', 'minifyjs', 'dbunit', 'phpunit'];

		foreach ($binFiles as $file) {
			$filePath = $rootFolder . '/bin/' . $file;
			if (is_link($filePath)) {
				$linkDestination = readlink($filePath);
				$fileRealPath = realpath($filePath);
				if (strncmp($linkDestination, '../vendor/', strlen('../vendor/')) === 0 // relative link to vendor folder
					|| $filePath === false // target don't exists, so link is broken
					|| strncmp(
						$fileRealPath,
						$oldVendorFolder,
						strlen($oldVendorFolder)
					) === 0 // still pointing to old vendor folder
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
		if (file_exists($oldVendorFolder . '/do_not_clean.txt')) {
			$io->write('');
			$io->write('File vendor/do_not_clean.txt is present, no attempt to clean the vendor folder will be done!');
			$io->write('');

			return;
		}

		// 2) If there is a vendor/autoload.php, check the hash of the folder structure, if different from at the time of the vendor_bundle migration, ignore
		if (file_exists($oldVendorFolder . '/autoload.php')) {

			$finder = new Finder();
			$finder->in($oldVendorFolder)->exclude(['Composer'])->depth(2);

			$packages = [];
			foreach ($finder as $file) {
				$packages[] = $file->getRelativePath();
			}

			$packages = array_unique($packages);
			natcasesort($packages);
			$packagesString = implode(':', array_values($packages));

			$md5checksum = md5($packagesString);

			if ($md5checksum != self::PRE_MIGRATION_OLD_VENDOR_FOLDER_MD5_HASH) {
				return;
			}

		}

		// 3) If we arrive here, clean all folders and autoload.php in the old (pre migration) vendor folder

		$fs->remove($oldVendorFolder . '/autoload.php');

		$vendorDirsCleaned = false;
		$vendorDirs = glob($oldVendorFolder . '/*', GLOB_ONLYDIR);
		foreach ($vendorDirs as $dir) {
			if (is_dir($dir)) {
				$fs->remove($dir);
				$vendorDirsCleaned = true;
			}
		}

		if ($vendorDirsCleaned) {
			// there are some cached templates that will stop tiki to work after the migration
			$loopDirs = array_merge(
				[$rootFolder . '/temp/templates_c'],
				glob($rootFolder . '/temp/templates_c/*', GLOB_ONLYDIR)
			);
			foreach ($loopDirs as $dir) {
				$cachedTemplates = glob($dir . '/*.tpl.php');
				foreach ($cachedTemplates as $template) {
					$fs->remove($template);
				}
			}
		}
	}
}


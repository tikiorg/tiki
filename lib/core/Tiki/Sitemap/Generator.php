<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Sitemap;

use Perms;
use SitemapPHP\Sitemap;

/**
 * Generate XML files following the XML Protocol that can be submitted to search engines
 */
class Generator
{
	/**
	 * The prefix to be added when scanning the sub folder for valid type handlers
	 */
	const NAMESPACE_PREFIX = '\\Tiki\\Sitemap\\Type\\';

	/**
	 * The base class the type handlers must extend
	 */
	const BASE_CLASS = '\\Tiki\\Sitemap\\AbstractType';

	protected $basePath;

	public function __construct($basePath = null)
	{
		global $tikipath;
		if (is_null($basePath)) {
			$basePath = $tikipath;
		}

		$this->basePath = $basePath;

		if (! function_exists('filter_our_sefurl') && file_exists($basePath . 'tiki-sefurl.php')) {
			include_once($basePath . 'tiki-sefurl.php');
		}
	}

	/**
	 * Function for generate sitemap XML
	 * @param string $baseUrl
	 */
	public function generate($baseUrl)
	{
		/** @var \Perms $perms */
		$perms = Perms::getInstance();
		$oldGroups = $perms->getGroups();
		$perms->setGroups(['Anonymous']); // ensure that permissions are processed as Anonymous

		$sitemap = new Sitemap($baseUrl);
		$sitemap->setPath($this->basePath . 'temp/public/');
		$sitemap->setFilename('sitemap');

		// Add the website root
		$sitemap->addItem('/', '1.0', 'daily', date('Y-m-d'));

		// Execute all other handlers, for the different type of content
		$directoryFiles = new \GlobIterator(__DIR__ . '/Type/*.php');
		/** @var \SplFileInfo $file */
		foreach ($directoryFiles as $file) {
			if ($file->getFilename() === 'index.php'){
				continue; // file to prevent directory browsing
			}

			$name = $file->getBasename('.php');
			$class = self::NAMESPACE_PREFIX . $name;

			if (! class_exists($class)) {
				continue;
			}

			/** @var AbstractType $typeHandler */
			$typeHandler = new $class($sitemap);
			if (is_subclass_of($typeHandler, self::BASE_CLASS)) {

				$typeHandler->generate();

			}
		}

		$sitemap->createSitemapIndex($baseUrl . '/public/', date('Y-m-d'));

		$perms->setGroups($oldGroups); // restore the group configuration for permissions
	}
}
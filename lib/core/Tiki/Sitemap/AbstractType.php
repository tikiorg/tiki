<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Sitemap;

use Perms;

/**
 * Abstract class for Sitemap Entries generation
 *
 * Has all the helper methods, child classes need to override "generate"
 */
abstract class AbstractType
{
	/**
	 * @var \SitemapPHP\Sitemap
	 */
	protected $sitemap;

	/**
	 * AbstractType constructor.
	 * @param \SitemapPHP\Sitemap $sitemap
	 */
	public function __construct($sitemap)
	{
		$this->sitemap = $sitemap;
	}

	/**
	 * Generate Sitemap: Function to be override in child classes where the entries for the sitemap are generated
	 */
	abstract public function generate();

	/**
	 * Check if the feature is available and if a given permission is granted
	 *
	 * @param string $feature
	 * @param string $permission
	 * @return bool
	 */
	protected function checkFeatureAndPermissions($feature, $permission = '')
	{
		global $prefs;

		if (empty($feature)) {
			return false;
		}

		if ($prefs[$feature] != 'y') {
			return false;
		}

		if (empty($permission)) {
			return true;
		}

		$perms = Perms::get();
		if ($perms->{$permission} != 'y') {
			return false;
		}

		return true;
	}

	/**
	 * Add a list of entries to the Site Map
	 *
	 * @param array $entries the entries will be taken from the key "data"
	 * @param string $urlTemplate Url template, where will be replaced the value of $idField
	 * @param string|int $idField Key for to field to be used to replace in the template
	 * @param string $entryType Type of entry (for SEF)
	 * @param string $titleField Field with the title of entry (for SEF)
	 * @param string $updateField Field with the last update date for the entry
	 * @param string $priority Priority to assign in sitemap (default 0.6)
	 * @param string $changeFrequency How frequent is the content to change (default weekly)
	 */
	protected function addEntriesToSitemap($entries, $urlTemplate, $idField, $entryType,
		$titleField = 'title', $updateField = 'created', $priority = '0.6', $changeFrequency = 'weekly'
	) {
		if (! isset($entries['data'])) {
			return;
		}

		foreach ($entries['data'] as $entry) {

			$url = sprintf($urlTemplate, urlencode($entry[$idField]));
			if (function_exists('filter_our_sefurl')) {
				$url = filter_out_sefurl($url, $entryType, (empty($titleField) || empty($entryType[$titleField])) ? '' : $entry[$titleField]);
			}

			$this->sitemap->addItem($url, $priority, $changeFrequency, $entry[$updateField]);
		}
	}
}
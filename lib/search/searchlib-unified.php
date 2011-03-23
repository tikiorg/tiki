<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class UnifiedSearchLib
{
	const INCREMENT_QUEUE = 'search-increment';

	function processUpdateQueue($count = 10)
	{
		if ($this->rebuildInProgress()) {
			return;
		}

		$toProcess = TikiLib::lib('queue')->pull(self::INCREMENT_QUEUE, $count);

		if (count($toProcess)) {
			$indexer = $this->buildIndexer($this->getIndex());
			$indexer->update($toProcess);
		}
	}

	private function rebuildInProgress() {
		global $prefs;
		$tempName = $prefs['unified_lucene_location'] . '-new';

		return file_exists($tempName);
	}

	function rebuild()
	{
		global $prefs;
		$tempName = $prefs['unified_lucene_location'] . '-new';
		$swapName = $prefs['unified_lucene_location'] . '-old';

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($tempName);
		} else {
			die('Unsupported');
		}

		// Build in -new
		$indexer = $this->buildIndexer($index);
		$stat = $indexer->rebuild();

		// Force destruction to clear locks
		unset($indexer);
		unset($index);

		if ($prefs['unified_engine'] == 'lucene') {
			// Current to -old
			if (file_exists($prefs['unified_lucene_location'])) {
				rename($prefs['unified_lucene_location'], $swapName);
			}
			// -new to current
			rename($tempName, $prefs['unified_lucene_location']);

			// Destroy old
			$this->destroyDirectory($swapName);
		}

		// Process the documents updated while we were processing the update
		$this->processUpdateQueue(1000);

		return $stat;
	}

	function invalidateObject($type, $objectId)
	{
		TikiLib::lib('queue')->push(self::INCREMENT_QUEUE, array(
			'object_type' => $type,
			'object_id' => $objectId
		));
	}

	public function getSupportedTypes()
	{
		global $prefs;
		$types = array();

		if ($prefs['feature_wiki'] == 'y') {
			$types['wiki page'] = tra('wiki page');
		}

		if ($prefs['feature_blogs'] == 'y') {
			$types['blog post'] = tra('blog post');
		}
		
		if ($prefs['feature_articles'] == 'y') {
			$types['article'] = tra('article');
		}

		if ($prefs['feature_forums'] == 'y') {
			$types['forum post'] = tra('forum post');
		}

		if ($prefs['feature_trackers'] == 'y') {
			$types['trackeritem'] = tra('trackeritem');
		}

		if ($prefs['feature_sheet'] == 'y') {
			$types['sheet'] = tra('sheet');
		}

		return $types;
	}

	private function buildIndexer($index)
	{
		$indexer = new Search_Indexer($index);
		$this->addSources($indexer);

		return $indexer;
	}

	private function addSources($aggregator, $mode = 'indexing')
	{
		$types = $this->getSupportedTypes();

		// Content Sources
		if (isset ($types['wiki page'])) {
			$aggregator->addContentSource('wiki page', new Search_ContentSource_WikiSource);
		}

		if (isset ($types['forum post'])) {
			$aggregator->addContentSource('forum post', new Search_ContentSource_ForumPostSource);
		}

		if (isset ($types['blog post'])) {
			$aggregator->addContentSource('blog post', new Search_ContentSource_BlogPostSource);
		}

		if (isset ($types['article'])) {
			$aggregator->addContentSource('article', new Search_ContentSource_ArticleSource);
		}

		if (isset ($types['file'])) {
			$aggregator->addContentSource('file', new Search_ContentSource_FileSource);
		}

		if (isset ($types['trackeritem'])) {
			$aggregator->addContentSource('trackeritem', new Search_ContentSource_TrackerItemSource);
		}

		if (isset ($types['sheet'])) {
			$aggregator->addContentSource('sheet', new Search_ContentSource_SheetSource);
		}

		global $prefs;

		// Global Sources
		if ($prefs['feature_categories'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_CategorySource);
		}

		if ($prefs['feature_freetags'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_FreeTagSource);
		}

		if ($prefs['rating_advanced'] == 'y' && $mode == 'indexing') {
			$aggregator->addGlobalSource(new Search_GlobalSource_AdvancedRatingSource);
		}

		if ($mode == 'indexing') {
			$aggregator->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance(), 'Admins'));
		}
	}

	function getIndex()
	{
		global $prefs;

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($prefs['unified_lucene_location']);

			return $index;
		}
	}

	function getDataSource($mode = 'indexing')
	{
		$dataSource = new Search_Formatter_DataSource_Declarative;
		$this->addSources($dataSource, $mode);

		return $dataSource;
	}

	function buildQuery(array $filter)
	{
		global $categlib; require_once 'lib/categories/categlib.php';

		$query = new Search_Query;
		$query->filterPermissions(Perms::get()->getGroups());

		if ($jail = $categlib->get_jail()) {
			$query->filterCategory($jail, true);
		}

		if (isset($filter['type']) && $filter['type']) {
			$query->filterType($filter['type']);
		}

		if (isset($filter['categories']) && $filter['categories']) {
			$query->filterCategory($filter['categories'], isset($filter['deep']));
		}

		if (isset($filter['tags']) && $filter['tags']) {
			$query->filterTags($filter['tags']);
		}

		if (isset($filter['content']) && $filter['content']) {
			$query->filterContent($filter['content']);
		}

		if (isset($filter['language']) && $filter['language']) {
			$q = "\"{$filter['language']}\"";

			if (isset($filter['language_unspecified'])) {
				$q .= ' or unknown';
			}

			$query->filterLanguage($q);
		}

		return $query;
	}

	private function destroyDirectory($path)
	{
		if (!$path or !is_dir($path)) return 0;

		if ($dir = opendir($path)) {
			while (false !== ($file = readdir($dir))) {
				if ($file == '.' || $file == '..') {
					continue;
				}

				if (is_dir($path . "/" . $file)) {
					$this->destroyDirectory($path . "/" . $file);
				} else {
					unlink($path . "/" . $file);
				}
			}
			closedir($dir);
		}

		rmdir($path);
	}
}

global $unifiedsearchlib;
$unifiedsearchlib = new UnifiedSearchLib;

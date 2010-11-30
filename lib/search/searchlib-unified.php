<?php

class UnifiedSearchLib
{
	private $incrementalQueue = array();

	function processUpdateQueue()
	{
		if (count($this->incrementalQueue)) {
			$indexer = $this->buildIndexer($this->getIndex());
			$indexer->update($this->incrementalQueue);
			$this->incrementalQueue = array();
		}
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
		$indexer->rebuild();

		// Force destruction to clear locks
		unset($indexer);
		unset($index);

		if ($prefs['unified_engine'] == 'lucene') {
			// Current to -old
			rename($prefs['unified_lucene_location'], $swapName);
			// -new to current
			rename($tempName, $prefs['unified_lucene_location']);

			// Destroy old
			$this->destroyDirectory($swapName);
		}
	}

	function invalidateObject($type, $objectId)
	{
		$this->incrementalQueue[] = array('object_type' => $type, 'object_id' => $objectId);
	}

	private function buildIndexer($index)
	{
		$indexer = new Search_Indexer($index);
		$this->addSources($indexer);

		return $indexer;
	}

	private function addSources($aggregator)
	{
		global $prefs;

		if ($prefs['feature_wiki'] == 'y') {
			$aggregator->addContentSource('wiki page', new Search_ContentSource_WikiSource);
		}

		if ($prefs['feature_forums'] == 'y') {
			$aggregator->addContentSource('forum post', new Search_ContentSource_ForumPostSource);
		}

		if ($prefs['feature_blogs'] == 'y') {
			$aggregator->addContentSource('blog post', new Search_ContentSource_BlogPostSource);
		}

		if ($prefs['feature_categories'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_CategorySource);
		}

		$aggregator->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance(), 'Admins'));
	}

	function getIndex()
	{
		global $prefs;

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($prefs['unified_lucene_location']);

			return $index;
		}
	}

	function getDataSource()
	{
		$dataSource = new Search_Formatter_DataSource_Declarative;
		$this->addSources($dataSource);

		return $dataSource;
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

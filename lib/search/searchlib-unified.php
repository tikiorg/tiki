<?php

class UnifiedSearchLib
{
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

	private function buildIndexer($index)
	{
		global $prefs;

		$indexer = new Search_Indexer($index);

		if ($prefs['feature_wiki'] == 'y') {
			$indexer->addContentSource('wiki page', new Search_ContentSource_WikiSource);
		}

		if ($prefs['feature_categories'] == 'y') {
			$indexer->addGlobalSource(new Search_GlobalSource_CategorySource);
		}

		$indexer->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance(), 'Admins'));
		$indexer->rebuild();

		return $indexer;
	}

	function getIndex()
	{
		global $prefs;

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($prefs['unified_lucene_location']);

			return $index;
		}
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

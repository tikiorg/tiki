<?php

class UnifiedSearchLib
{
	function rebuild()
	{
		global $prefs;
		$tempName = $prefs['unified_lucene_location'] . '-new';

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($tempName);
		} else {
			die('Unsupported');
		}

		$indexer = $this->buildIndexer($index);

		unset($indexer);
		unset($index);

		if ($prefs['unified_engine'] == 'lucene') {
			$this->destroyDirectory($prefs['unified_lucene_location']);

			rename($tempName, $prefs['unified_lucene_location']);
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

		$indexer->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance()));
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

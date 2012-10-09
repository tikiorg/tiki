<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class UnifiedSearchLib
{
	const INCREMENT_QUEUE = 'search-increment';
	private $batchToken;

	function startBatch()
	{
		if (! $this->batchToken) {
			$this->batchToken = uniqid();
			return $this->batchToken;
		}
	}

	function endBatch($token, $count = 100)
	{
		if ($token && $this->batchToken === $token) {
			$this->batchToken = null;
			$this->processUpdateQueue($count);
		}
	}

	function processUpdateQueue($count = 10)
	{
		if ($this->batchToken) {
			return;
		}

		if ($this->rebuildInProgress()) {
			return;
		}

		$queuelib = TikiLib::lib('queue');
		$toProcess = $queuelib->pull(self::INCREMENT_QUEUE, $count);
		$errlib = TikiLib::lib('errorreport');

		if (count($toProcess)) {
			try {
				$indexer = $this->buildIndexer($this->getIndex());
				$indexer->update($toProcess);
			} catch (Zend_Search_Lucene_Exception $e) {
				// Re-queue pulled messages for next update
				foreach ($toProcess as $message) {
					$queuelib->push(self::INCREMENT_QUEUE, $message);
				}

				$errlib->report(
					tr('Search index could not be updated. The site is misconfigured. Contact an administrator.') .
					'<br />' . $e->getMessage()
				);
			}
		}
	}

	function getQueueCount()
	{
		$queuelib = TikiLib::lib('queue');
		return $queuelib->count(self::INCREMENT_QUEUE);
	}

	function rebuildInProgress()
	{
		$tempName = $this->getIndexLocation() . '-new';
		$file_exists = file_exists($tempName);

		if (!isset($_SERVER['REQUEST_METHOD']) && !TikiInit::isWindows()) {		// called from shell.php and unix?
			$output = null;
			exec('ps ax | grep \'search/shell.php\'|grep -v grep', $output);	// check for another running process
			if (is_array($output) && count($output) > 1) {
				return true;
			} else if ($file_exists) {
				$this->destroyDirectory($tempName);
				$file_exists = false;
			}
		}
		return $file_exists;
	}

	function rebuild($loggit = false)
	{
		global $prefs;
		$index_location = $this->getIndexLocation();
		$tempName = $index_location . '-new';
		$swapName = $index_location . '-old';

		if ($prefs['unified_engine'] == 'lucene') {
			$index = new Search_Index_Lucene($tempName);
		} else {
			die('Unsupported');
		}

		@ini_set('max_execution_time', 0);
		@ini_set('memory_limit', -1);

		// Build in -new
		TikiLib::lib('queue')->clear(self::INCREMENT_QUEUE);
		$indexer = $this->buildIndexer($index, $loggit);
		$stat = $indexer->rebuild();

		// Force destruction to clear locks
		unset($indexer);
		unset($index);

		if ($prefs['unified_engine'] == 'lucene') {
			// Current to -old
			if (file_exists($index_location)) {
				rename($index_location, $swapName);
			}
			// -new to current
			rename($tempName, $index_location);

			// Destroy old
			$this->destroyDirectory($swapName);
		}

		// Process the documents updated while we were processing the update
		$this->processUpdateQueue(1000);

		return $stat;
	}

	/**
	 * Get the index location depending on $tikidomain for multi-tiki
	 *
	 * @return string	path to index directory
	 */
	private function getIndexLocation()
	{
		global $prefs, $tikidomain;
		$loc = $prefs['unified_lucene_location'];
		$temp = $prefs['tmpDir'];
		if (!empty($tikidomain) && strpos($loc, $tikidomain) === false && strpos($loc, "$temp/") === 0) {
			$loc = str_replace("$temp/", "$temp/$tikidomain/", $loc);
		}

		return $loc;
	}

	function invalidateObject($type, $objectId)
	{
		TikiLib::lib('queue')->push(
			self::INCREMENT_QUEUE,
			array(
				'object_type' => $type,
				'object_id' => $objectId
			)
		);
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

		if ($prefs['feature_file_galleries'] == 'y') {
			$types['file'] = tra('file');
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

		if ($prefs['feature_wiki_comments'] == 'y'
			|| $prefs['feature_article_comments'] == 'y'
			|| $prefs['feature_poll_comments'] == 'y'
			|| $prefs['feature_file_galleries_comments'] == 'y'
			|| $prefs['feature_trackers'] == 'y'
		) {
			$types['comment'] = tra('comment');
		}

		return $types;
	}

	private function buildIndexer($index, $loggit = false)
	{
		global $prefs;
		$indexer = new Search_Indexer($index, $loggit);
		$this->addSources($indexer);

		if ($prefs['unified_tokenize_version_numbers'] == 'y') {
			$indexer->addContentFilter(new Search_ContentFilter_VersionNumber);
		}

		return $indexer;
	}

	private function addSources($aggregator, $mode = 'indexing')
	{
		global $prefs;

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
			$aggregator->addGlobalSource(new Search_GlobalSource_FileAttachmentSource);
		}

		if (isset ($types['trackeritem'])) {
			$aggregator->addContentSource('trackeritem', new Search_ContentSource_TrackerItemSource);
		}

		if (isset ($types['sheet'])) {
			$aggregator->addContentSource('sheet', new Search_ContentSource_SheetSource);
		}

		if (isset($types['comment'])) {
			$commentTypes = array();
			if ($prefs['feature_wiki_comments'] == 'y') {
				$commentTypes[] = 'wiki page';
			}
			if ($prefs['feature_article_comments'] == 'y') {
				$commentTypes[] = 'article';
			}
			if ($prefs['feature_poll_comments'] == 'y') {
				$commentTypes[] = 'poll';
			}
			if ($prefs['feature_file_galleries_comments'] == 'y') {
				$commentTypes[] = 'file gallery';
			}
			if ($prefs['feature_trackers'] == 'y') {
				$commentTypes[] = 'trackeritem';
			}

			$aggregator->addContentSource('comment', new Search_ContentSource_CommentSource($commentTypes));
			$aggregator->addGlobalSource(new Search_GlobalSource_CommentSource);
		}

		// Global Sources
		if ($prefs['feature_categories'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_CategorySource);
		}

		if ($prefs['feature_freetags'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_FreeTagSource);
		}

		if ($prefs['rating_advanced'] == 'y' && $mode == 'indexing') {
			$aggregator->addGlobalSource(new Search_GlobalSource_AdvancedRatingSource($prefs['rating_recalculation'] == 'indexing'));
		}

		$aggregator->addGlobalSource(new Search_GlobalSource_Geolocation);

		if ($prefs['feature_search_show_visit_count'] === 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_VisitsSource);
		}

		if ($mode == 'indexing') {
			$aggregator->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance()));
			$aggregator->addGlobalSource(new Search_GlobalSource_RelationSource);
		}
	}

	function getIndex()
	{
		global $prefs;

		if ($prefs['unified_engine'] == 'lucene') {
			Zend_Search_Lucene::setTermsPerQueryLimit($prefs['unified_lucene_terms_limit']);
			$index = new Search_Index_Lucene($this->getIndexLocation(), $prefs['language'], $prefs['unified_lucene_highlight'] == 'y');
			$index->setCache(TikiLib::lib('cache'));
			$index->setMaxResults($prefs['unified_lucene_max_result']);
			$index->setResultSetLimit($prefs['unified_lucene_max_resultset_limit']);

			return $index;
		}
	}

	function getDataSource($mode = 'indexing')
	{
		$dataSource = new Search_Formatter_DataSource_Declarative;
		$this->addSources($dataSource, $mode);

		return $dataSource;
	}

	function getWeightCalculator()
	{
		global $prefs;

		$lines = explode("\n", $prefs['unified_field_weight']);

		$weights = array();
		foreach ($lines as $line) {
			$parts = explode(':', $line, 2);
			if (count($parts) == 2) {
				$parts = array_map('trim', $parts);

				$weights[$parts[0]] = $parts[1];
			}
		}

		return new Search_Query_WeightCalculator_Field($weights);
	}

	function buildQuery(array $filter)
	{
		$categlib = TikiLib::lib('categ');

		$query = new Search_Query;
		$query->setWeightCalculator($this->getWeightCalculator());

		if (! Perms::get()->admin) {
			$query->filterPermissions(Perms::get()->getGroups());
		}
		$jail_query = '';

		if ($jail = $categlib->get_jail()) {
			$i = 0;
			foreach ($jail as $cat) {
				$i++;
				$jail_query .= $cat;
				if ($i < count($jail)) {
					$jail_query .= ' or ';
				}
			}
			$query->filterCategory($jail_query, true);
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
			$query->filterContent($filter['content'], TikiLib::lib('tiki')->get_preference('unified_default_content', array('contents'), true));
		}

		if (isset($filter['autocomplete']) && $filter['autocomplete']) {
			$query->filterInitial($filter['autocomplete']);
		}

		if (isset($filter['language']) && $filter['language']) {
			$q = $filter['language'];
			if (preg_match('/^\w+\-\w+$/', $q)) {
				$q = "\"$q\"";
			}

			if (isset($filter['language_unspecified'])) {
				$q = "($q) or unknown";
			}

			$query->filterLanguage($q);
		}

		unset($filter['type']);
		unset($filter['categories']);
		unset($filter['deep']);
		unset($filter['tags']);
		unset($filter['content']);
		unset($filter['language']);
		unset($filter['language_unspecified']);
		unset($filter['autocomplete']);

		foreach ($filter as $key => $value) {
			if ($value) {
				$query->filterContent($value, $key);
			}
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

				if (is_dir($path . '/' . $file)) {
					$this->destroyDirectory($path . '/' . $file);
				} else {
					unlink($path . '/' . $file);
				}
			}
			closedir($dir);
		}

		rmdir($path);
	}
}

global $unifiedsearchlib;
$unifiedsearchlib = new UnifiedSearchLib;

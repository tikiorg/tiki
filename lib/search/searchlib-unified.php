<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class UnifiedSearchLib
{
	const INCREMENT_QUEUE = 'search-increment';
	private $batchToken;

    /**
     * @return string
     */
    function startBatch()
	{
		if (! $this->batchToken) {
			$this->batchToken = uniqid();
			return $this->batchToken;
		}
	}

    /**
     * @param $token
     * @param int $count
     */
    function endBatch($token, $count = 100)
	{
		if ($token && $this->batchToken === $token) {
			$this->batchToken = null;
			$this->processUpdateQueue($count);
		}
	}

    /**
     * @param int $count
     */
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
		$access = TikiLib::lib('access');
		$access->preventRedirect(true);

		if (count($toProcess)) {
			try {
				$indexer = $this->buildIndexer($this->getIndex());
				$indexer->update($toProcess);
			} catch (Exception $e) {
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

		$access->preventRedirect(false);
	}

    /**
     * @return array
     */
    function getQueueCount()
	{
		$queuelib = TikiLib::lib('queue');
		return $queuelib->count(self::INCREMENT_QUEUE);
	}

    /**
     * @return bool
     */
    function rebuildInProgress()
	{
		global $prefs;
		if ($prefs['unified_engine'] == 'lucene') {
			$tempName = $this->getIndexLocation() . '-new';
			$new_exists = file_exists($this->getIndexLocation() . '-new');
			$old_exists = file_exists($this->getIndexLocation() . '-old');

			return $new_exists || $old_exists;
		}

		return false;
	}

	/**
	 */
	function stopRebuild()
	{
		global $prefs;
		if ($prefs['unified_engine'] == 'lucene') {
			$tempName = $this->getIndexLocation() . '-new';
			$file_exists = file_exists($tempName);
			if ($file_exists) {
				$this->destroyDirectory($tempName);
			}
			$tempName = $this->getIndexLocation() . '-old';
			$file_exists = file_exists($tempName);
			if ($file_exists) {
				$this->destroyDirectory($tempName);
			}
		}
	}

    /**
     * @param bool $loggit
     * @return array
     */
    function rebuild($loggit = false)
	{
		global $prefs;
		$errlib = TikiLib::lib('errorreport');

		switch ($prefs['unified_engine']) {
		case 'lucene':
			$index_location = $this->getIndexLocation();
			$tempName = $index_location . '-new';
			$swapName = $index_location . '-old';
			
			if ($this->rebuildInProgress()) {
				$errlib->report(tr('Rebuild in progress.'));
				return false;
			}

			$index = new Search_Index_Lucene($tempName);

			$unifiedsearchlib = $this;
			register_shutdown_function(
				function () use ($tempName, $unifiedsearchlib) {
					if (file_exists($tempName)) {
						$unifiedsearchlib->destroyDirectory($tempName);
						echo "Abnormal termination. Unless it was killed manually, it likely ran out of memory.\n";
					}
				}
			);
			break;
		case 'elastic':
			$connection = $this->getElasticConnection();
			$indexName = $prefs['unified_elastic_index_prefix'] . uniqid();
			$index = new Search_Elastic_Index($connection, $indexName);

			register_shutdown_function(
				function () use ($indexName, $index) {
					global $prefs;
					if ($prefs['unified_elastic_index_current'] !== $indexName) {
						$index->destroy();
					}
				}
			);
			break;
		default:
			die('Unsupported');
		}


		// Build in -new
		TikiLib::lib('queue')->clear(self::INCREMENT_QUEUE);
		$tikilib = TikiLib::lib('tiki');
		$access = TikiLib::lib('access');
		$access->preventRedirect(true);
		$index = new Search_Index_TypeAnalysisDecorator($index);
		$indexer = $this->buildIndexer($index, $loggit);
		$stat = $tikilib->allocate_extra(
			'unified_rebuild',
			function () use ($indexer) {
				return $indexer->rebuild();
			}
		);
		$access->preventRedirect(false);

		$tikilib->set_preference('unified_identifier_fields', $index->getIdentifierFields());

		// Force destruction to clear locks
		unset($indexer);
		unset($index);

		switch ($prefs['unified_engine']) {
		case 'lucene':
			// Current to -old
			if (file_exists($index_location)) {
				if (! rename($index_location, $swapName)) {
					$errlib->report(tr('Could not remove active index. Likely a file permission issue.'));
				}
			}
			// -new to current
			if (! rename($tempName, $index_location)) {
				$errlib->report(tr('Could not transfer new index to active. Likely a file permission issue.'));
			}

			// Destroy old
			$this->destroyDirectory($swapName);

			if (file_exists($swapName)) {
				$errlib->report(tr('Failed to destroy the old index. Likely a file permission issue.'));
			}
			break;
		case 'elastic':
			// Obtain the old index and destroy it after permanently replacing it.
			$oldIndex = $this->getIndex();

			$tikilib->set_preference('unified_elastic_index_current', $indexName);

			if ($oldIndex) {
				$oldIndex->destroy();
			}
			break;
		}

		// Process the documents updated while we were processing the update
		$this->processUpdateQueue(1000);

		$tikilib->set_preference('unified_last_rebuild', $tikilib->now);
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

    /**
     * @param $type
     * @param $objectId
     */
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

    /**
     * @return array
     */
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

		if (in_array($prefs['user_in_search_result'], array('all', 'public'))) {
			$types['user'] = tra('user');
		}

		return $types;
	}

    /**
     * @param $index
     * @param bool $loggit
     * @return Search_Indexer
     */
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

    /**
     * @param $aggregator
     * @param string $mode
     */
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

		if (isset($types['user'])) {
			$aggregator->addContentSource('user', new Search_ContentSource_UserSource($prefs['user_in_search_result']));
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

    /**
     * @return Search_Index_Lucene
     */
    function getIndex()
	{
		global $prefs;

		switch ($prefs['unified_engine']) {
		case 'lucene':
			Zend_Search_Lucene::setTermsPerQueryLimit($prefs['unified_lucene_terms_limit']);
			$index = new Search_Index_Lucene($this->getIndexLocation(), $prefs['language'], $prefs['unified_lucene_highlight'] == 'y');
			$index->setCache(TikiLib::lib('cache'));
			$index->setMaxResults($prefs['unified_lucene_max_result']);
			$index->setResultSetLimit($prefs['unified_lucene_max_resultset_limit']);

			return $index;
		case 'elastic':
			if (empty($prefs['unified_elastic_index_current'])) {
				return null;
			}

			$connection = $this->getElasticConnection();
			$index = new Search_Elastic_Index($connection, $prefs['unified_elastic_index_current']);
			return $index;
		}
	}

	private function getElasticConnection()
	{
		global $prefs;
		$connection = new Search_Elastic_Connection($prefs['unified_elastic_url']);
		$connection->startBulk();

		return $connection;
	}

    /**
     * @param string $mode
     * @return Search_Formatter_DataSource_Declarative
     */
    function getDataSource($mode = 'indexing')
	{
		$dataSource = new Search_Formatter_DataSource_Declarative;
		$this->addSources($dataSource, $mode);

		return $dataSource;
	}

	function getProfileExportHelper()
	{
		$helper = new Tiki_Profile_Writer_SearchFieldHelper;
		$this->addSources($helper);

		return $helper;
	}

    /**
     * @return Search_Query_WeightCalculator_Field
     */
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

	function initQuery(Search_Query $query)
	{
		global $prefs;

		$query->setWeightCalculator($this->getWeightCalculator());
		$query->setIdentifierFields($prefs['unified_identifier_fields']);

		if (! Perms::get()->admin) {
			$query->filterPermissions(Perms::get()->getGroups());
		}

		$categlib = TikiLib::lib('categ');
		if ($jail = $categlib->get_jail()) {
			$query->filterCategory(implode(' or ', $jail), true);
		}
	}

    /**
     * @param array $filter
     * @return Search_Query
     */
    function buildQuery(array $filter)
	{
		$query = new Search_Query;
		$this->initQuery($query);

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
			$o = TikiLib::lib('tiki')->get_preference('unified_default_content', array('contents'), true);
			if (count($o) == 1 && empty($o[0])) {
				// Use "contents" field by default, if no default is specified
				$query->filterContent($filter['content'], array('contents'));
			} else {
				$query->filterContent($filter['content'], $o);
			}
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

    /**
	 * Private. Used by a callback, so made public until PHP 5.4.
	 *
     * @param $path
     * @return int
	 * @private
     */
	function destroyDirectory($path)
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

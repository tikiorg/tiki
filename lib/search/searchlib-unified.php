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
			$indexer = null;
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

			if ($indexer) {
				$indexer->clearSources();
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
			$new = $this->getIndex('data-new');
			$old = $this->getIndex('data-old');

			return $new->exists() || $old->exists();
		}

		return false;
	}

	/**
	 */
	function stopRebuild()
	{
		global $prefs;
		if ($prefs['unified_engine'] == 'lucene') {
			$this->getIndex('data-old')->destroy();
			$this->getIndex('data-new')->destroy();
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
			$index_location = $this->getIndexLocation('data');
			$tempName = $this->getIndexLocation('data-new');
			$swapName = $this->getIndexLocation('data-old');

			if ($this->rebuildInProgress()) {
				$errlib->report(tr('Rebuild in progress.'));
				return false;
			}

			$index = new Search_Lucene_Index($tempName);

			register_shutdown_function(
				function () use ($index) {
					if ($index->exists()) {
						$index->destroy();
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
		case 'mysql':
			$indexName = 'index_' . uniqid();
			$index = new Search_MySql_Index(TikiDb::get(), $indexName);

			register_shutdown_function(
				function () use ($indexName, $index) {
					global $prefs;
					if ($prefs['unified_mysql_index_current'] !== $indexName) {
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

		$stat = array();
		$indexer = null;
		try {
			$index = new Search_Index_TypeAnalysisDecorator($index);
			$indexer = $this->buildIndexer($index, $loggit);
			$stat = $tikilib->allocate_extra(
				'unified_rebuild',
				function () use ($indexer) {
					return $indexer->rebuild();
				}
			);

			$tikilib->set_preference('unified_identifier_fields', $index->getIdentifierFields());
		} catch (Exception $e) {
			$errlib->report(
				tr('Search index could not be rebuilt.') .
				'<br />' . $e->getMessage()
			);
		}

		$access->preventRedirect(false);

		// Force destruction to clear locks
		if ($indexer) {
			$indexer->clearSources();
			unset($indexer);
		}

		unset($index);

		$oldIndex = null;
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
			$oldIndex = new Search_Lucene_Index($swapName);
			break;
		case 'elastic':
			// Obtain the old index and destroy it after permanently replacing it.
			$oldIndex = $this->getIndex();

			$tikilib->set_preference('unified_elastic_index_current', $indexName);

			break;
		case 'mysql':
			// Obtain the old index and destroy it after permanently replacing it.
			$oldIndex = $this->getIndex();

			$tikilib->set_preference('unified_mysql_index_current', $indexName);

			break;
		}

		if ($oldIndex) {
			if (! $oldIndex->destroy()) {
				$errlib->report(tr('Failed to destroy the old index.'));
			}
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
	private function getIndexLocation($indexType = 'data')
	{
		global $prefs, $tikidomain;
		$mapping = array(
			'lucene' => array(
				'data' => $prefs['unified_lucene_location'],
				'data-old' => $prefs['unified_lucene_location'] . '-old',
				'data-new' => $prefs['unified_lucene_location'] . '-new',
				'preference' => $prefs['tmpDir'] . '/unified-preference-index-' . $prefs['language'],
			),
			'elastic' => array(
				'data' => $prefs['unified_elastic_index_current'],
				'preference' => $prefs['unified_elastic_index_prefix'] . 'pref_' . $prefs['language'],
			),
			'mysql' => array(
				'data' => $prefs['unified_mysql_index_current'],
				'preference' => 'index_' . 'pref_' . $prefs['language'],
			),
		);

		$engine = $prefs['unified_engine'];

		if (isset($mapping[$engine][$indexType])) {
			$index = $mapping[$engine][$indexType];

			if ($engine == 'lucene' && ! empty($tikidomain)) {
				$temp = $prefs['tmpDir'];
				if (strpos($index, $tikidomain) === false && strpos($index, "$temp/") === 0) {
					$index = str_replace("$temp/", "$temp/$tikidomain/", $index);
				}
			}

			return $index;
		} else {
			throw new Exception('Internal: Invalid index requested: ' . $indexType);
		}
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

		if (! empty($prefs['unified_excluded_categories'])) {
			$index = new Search_Index_CategoryFilterDecorator($index, array_filter($prefs['unified_excluded_categories']));
		}

		$logWriter = null;

		if ($loggit) {
			$logWriter = new Zend_Log_Writer_Stream($prefs['tmpDir'] . '/Search_Indexer.log', 'w');
		}

		$indexer = new Search_Indexer($index, $logWriter);
		$this->addSources($indexer, 'indexing');

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

		if ($prefs['activity_custom_events'] == 'y') {
			$aggregator->addContentSource('activity', new Search_ContentSource_ActivityStreamSource($aggregator instanceof Search_Indexer ? $aggregator : null));
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

		if ($prefs['feature_friends'] === 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_SocialSource);
		}

		if ($mode == 'indexing') {
			$aggregator->addGlobalSource(new Search_GlobalSource_PermissionSource(Perms::getInstance()));
			$aggregator->addGlobalSource(new Search_GlobalSource_RelationSource);
		}
	}

    /**
     * @return Search_Index_Interface
     */
    function getIndex($indexType = 'data')
	{
		global $prefs;

		switch ($prefs['unified_engine']) {
		case 'lucene':
			Zend_Search_Lucene::setTermsPerQueryLimit($prefs['unified_lucene_terms_limit']);
			$index = new Search_Lucene_Index($this->getIndexLocation($indexType), $prefs['language'], $prefs['unified_lucene_highlight'] == 'y');
			$index->setCache(TikiLib::lib('cache'));
			$index->setMaxResults($prefs['unified_lucene_max_result']);
			$index->setResultSetLimit($prefs['unified_lucene_max_resultset_limit']);

			return $index;
		case 'elastic':
			$index = $this->getIndexLocation($indexType);
			if (empty($index)) {
				break;
			}

			$connection = $this->getElasticConnection();
			$index = new Search_Elastic_Index($connection, $index);
			return $index;
		case 'mysql':
			$index = $this->getIndexLocation($indexType);
			if (empty($index)) {
				break;
			}

			$index = new Search_MySql_Index(TikiDb::get(), $index);
			return $index;
		}

		// Do nothing, provide a fake index.
		$errlib = TikiLib::lib('errorreport');
		$errlib->report(tr('No index available.'));

		return new Search_Index_Memory;
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
     * @return Search_Formatter_DataSource_Interface
     */
    function getDataSource($mode = 'formatting')
	{
		global $prefs;

		$dataSource = new Search_Formatter_DataSource_Declarative;

		$this->addSources($dataSource, $mode);

		if ($mode === 'formatting') {
			if ($prefs['unified_engine'] === 'mysql') {
				$dataSource->setPrefilter(
					function ($fields, $entry) {
						return array_filter(
							$fields,
							function ($field) use ($entry) {
								if (! empty($entry[$field])) {
									return preg_match('/token[a-z]{20,}/', $entry[$field]);
								}
							}
						);
					}
				);
			} elseif ($prefs['unified_engine'] === 'elastic') {
				$dataSource->setPrefilter(
					function ($fields, $entry) {
						return array_filter(
							$fields,
							function ($field) use ($entry) {
								return ! isset($entry[$field]);
							}
						);
					}
				);
			}
		}

		return $dataSource;
	}

	function getProfileExportHelper()
	{
		$helper = new Tiki_Profile_Writer_SearchFieldHelper;
		$this->addSources($helper, 'indexing'); // Need all fields, so use indexing

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

	function getFacetProvider()
	{
		global $prefs;
		$types = $this->getSupportedTypes();

		$facets = array(
			Search_Query_Facet_Term::fromField('object_type')
				->setLabel(tr('Object Type'))
				->setRenderMap($types),
		);

		if ($prefs['feature_multilingual'] == 'y') {
			$facets[] = Search_Query_Facet_Term::fromField('language')
				->setLabel(tr('Language'))
				->setRenderMap(TikiLib::lib('tiki')->get_language_map());
		}

		$provider = new Search_FacetProvider;
		$provider->addFacets($facets);
		$this->addSources($provider);

		return $provider;
	}
}

global $unifiedsearchlib;
$unifiedsearchlib = new UnifiedSearchLib;

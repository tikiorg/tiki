<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	private $isRebuildingNow = false;

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
			return true;
		}

		return false;
	}

    /**
     * @param int $count
     */
    function processUpdateQueue($count = 10)
	{
		global $prefs;
		if (! isset($prefs['unified_engine'])) {
			return;
		}

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
				// Since the object being updated may have category changes during the update,
				// make sure internal permission cache does not refer to the pre-update situation.
				Perms::getInstance()->clear();

				$index = $this->getIndex('data-write');
				$index = new Search_Index_TypeAnalysisDecorator($index);
				$indexer = $this->buildIndexer($index);
				$indexer->update($toProcess);

				if ($prefs['storedsearch_enabled'] == 'y') {
					// Stored search relation adding may cause residual index backlog
					$toProcess = $queuelib->pull(self::INCREMENT_QUEUE, $count);
					$indexer->update($toProcess);
				}

				// Detect newly created identifier fields
				$initial = array_flip($prefs['unified_identifier_fields']);
				$collected = array_flip($index->getIdentifierFields());
				$combined = array_merge($initial, $collected);

				// Store preference only on change
				if (count($combined) > count($initial)) {
					$tikilib = TikiLib::lib('tiki');
					$tikilib->set_preference('unified_identifier_fields', array_keys($combined));
				}
			} catch (Exception $e) {
				// Re-queue pulled messages for next update
				foreach ($toProcess as $message) {
					$queuelib->push(self::INCREMENT_QUEUE, $message);
				}

				$errlib->report(
					tr('The search index could not be updated. The site is misconfigured. Contact an administrator.') .
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
		} elseif ($prefs['unified_engine'] == 'elastic') {
			$name = $this->getIndexLocation('data');
			$connection = $this->getElasticConnection(true);
			return $connection->isRebuilding($name);
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
     * @param int $loggit 0=no logging, 1=log to Search_Indexer.log, 2=log to Search_Indexer_console.log
     * @return array
     */
    function rebuild($loggit = 0)
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

			TikiLib::events()->bind(
				'tiki.process.shutdown',
				function () use ($index) {
					if ($index->exists()) {
						$index->destroy();
						echo "Abnormal termination. Unless it was killed manually, it likely ran out of memory.\n";
					}
				}
			);
			break;
		case 'elastic':
			$connection = $this->getElasticConnection(true);
			$aliasName = $prefs['unified_elastic_index_prefix'] . 'main';
			$indexName = $aliasName . '_' . uniqid();
			$index = new Search_Elastic_Index($connection, $indexName);
			$index->setCamelCaseEnabled($prefs['unified_elastic_camel_case'] == 'y');

			TikiLib::events()->bind(
				'tiki.process.shutdown',
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

			TikiLib::events()->bind(
				'tiki.process.shutdown',
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

		$this->isRebuildingNow = true;

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
				tr('The search index could not be rebuilt.') .
				'<br />' . $e->getMessage()
			);
		}

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
					$errlib->report(tr('The active index could not be removed, probably due to a file permission issue.'));
				}
			}
			// -new to current
			if (! rename($tempName, $index_location)) {
				$errlib->report(tr('The new index could not be made active, probably due to a file permission issue.'));
			}

			// Destroy old
			$oldIndex = new Search_Lucene_Index($swapName);
			break;
		case 'elastic':
			$oldIndex = null; // assignAlias will handle the clean-up
			$tikilib->set_preference('unified_elastic_index_current', $indexName);
			$connection->assignAlias($aliasName, $indexName);

			break;
		case 'mysql':
			// Obtain the old index and destroy it after permanently replacing it.
			$oldIndex = $this->getIndex('data');

			$tikilib->set_preference('unified_mysql_index_current', $indexName);

			break;
		}

		if ($oldIndex) {
			if (! $oldIndex->destroy()) {
				$errlib->report(tr('Failed to delete the old index.'));
			}
		}

		// Process the documents updated while we were processing the update
		$this->processUpdateQueue(1000);

		if ($prefs['storedsearch_enabled'] == 'y') {
			TikiLib::lib('storedsearch')->reloadAll();
		}

		$tikilib->set_preference('unified_last_rebuild', $tikilib->now);

		$this->isRebuildingNow = false;
		$access->preventRedirect(false);
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
				'data' => $prefs['unified_elastic_index_prefix'] . 'main',
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
			$types['trackeritem'] = tra('tracker item');
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

		$types['user'] = tra('user');
		$types['group'] = tra('group');

		return $types;
	}


	function getLastLogItem() {
		global $prefs;
		$files['web'] = $prefs['tmpDir'] . '/Search_Indexer.log'; 
		$files['console'] = $prefs['tmpDir'] . '/Search_Indexer_console.log';
		foreach ($files as $type => $file) {
			if ( $fp = @fopen($file, "r") ) {	
				$pos = -2;
				$t = " ";
				while ($t != "\n") {
					if (!fseek($fp, $pos, SEEK_END)) {
						$t = fgetc($fp);
						$pos = $pos - 1;
					} else {
						rewind($fp);
						break;
					}
				}
				$t = fgets($fp);
				fclose($fp);
				$ret[$type] = $t;	
			} else {
				$ret[$type] = '';
			}
		}
		return $ret;
	}

    /**
     * @param $index
     * @param int $loggit 0=no logging, 1=log to Search_Indexer.log, 2=log to Search_Indexer_console.log
     * @return Search_Indexer
     */
    private function buildIndexer($index, $loggit = 0)
	{
		global $prefs;

		$isRepository = $index instanceof Search_Index_QueryRepository;
		
		if (! $isRepository && method_exists($index, 'getRealIndex')) {
			$isRepository = $index->getRealIndex() instanceof Search_Index_QueryRepository;
		}

		if (! $this->isRebuildingNow && $isRepository && $prefs['storedsearch_enabled'] == 'y') {
			$index = new Search_Index_QueryAlertDecorator($index);
		}

		if (! empty($prefs['unified_excluded_categories'])) {
			$index = new Search_Index_CategoryFilterDecorator($index,
				array_filter(
					array_map ('intval',
						$prefs['unified_excluded_categories']
					)
				)
			);
		}

		$logWriter = null;

		if ((int) $loggit == 1) {
			$logWriter = new Zend\Log\Writer\Stream($prefs['tmpDir'] . '/Search_Indexer.log', 'w');
		} elseif ((int) $loggit == 2) {
			$logWriter = new Zend\Log\Writer\Stream($prefs['tmpDir'] . '/Search_Indexer_console.log', 'w');
		}

		$indexer = new Search_Indexer($index, $logWriter);
		$this->addSources($indexer, 'indexing');

		if ($prefs['unified_tokenize_version_numbers'] == 'y') {
			$indexer->addContentFilter(new Search_ContentFilter_VersionNumber);
		}

		return $indexer;
	}

	public function getDocuments($type, $object)
	{
		$indexer = $this->buildIndexer($this->getIndex());
		return $indexer->getDocuments($type, $object);
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
			$aggregator->addContentSource('forum', new Search_ContentSource_ForumSource);
		}

		if (isset ($types['blog post'])) {
			$aggregator->addContentSource('blog post', new Search_ContentSource_BlogPostSource);
		}

		if (isset ($types['article'])) {
			$articleSource = new Search_ContentSource_ArticleSource;
			$aggregator->addContentSource('article', $articleSource);
			$aggregator->addGlobalSource(new Search_GlobalSource_ArticleAttachmentSource($articleSource));
		}

		if (isset ($types['file'])) {
			$fileSource = new Search_ContentSource_FileSource;
			$aggregator->addContentSource('file', $fileSource);
			$aggregator->addContentSource('file gallery', new Search_ContentSource_FileGallerySource);
			$aggregator->addGlobalSource(new Search_GlobalSource_FileAttachmentSource($fileSource));
		}

		if (isset ($types['trackeritem'])) {
			$aggregator->addContentSource('trackeritem', new Search_ContentSource_TrackerItemSource);
			$aggregator->addContentSource('tracker', new Search_ContentSource_TrackerSource);
			$aggregator->addContentSource('trackerfield', new Search_ContentSource_TrackerFieldSource);
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

		if (isset($types['group'])) {
			$aggregator->addContentSource('group', new Search_ContentSource_GroupSource);
		}

		if ($prefs['activity_custom_events'] == 'y' || $prefs['activity_basic_events'] == 'y' || $prefs['monitor_enabled'] == 'y') {
			$aggregator->addContentSource('activity', new Search_ContentSource_ActivityStreamSource($aggregator instanceof Search_Indexer ? $aggregator : null));
		}

		if ($prefs['goal_enabled'] == 'y') {
			$aggregator->addContentSource('goalevent', new Search_ContentSource_GoalEventSource);
		}

		// Global Sources
		if ($prefs['feature_categories'] == 'y') {
			$aggregator->addGlobalSource(new Search_GlobalSource_CategorySource);
			$aggregator->addContentSource('category', new Search_ContentSource_CategorySource);
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

		$aggregator->addGlobalSource(new Search_GlobalSource_TitleInitialSource);
		$aggregator->addGlobalSource(new Search_GlobalSource_SearchableSource);
		$aggregator->addGlobalSource(new Search_GlobalSource_UrlSource);
	}

    /**
     * @return Search_Index_Interface
     */
    function getIndex($indexType = 'data')
	{
		global $prefs, $tiki_p_admin;

		$writeMode = false;
		if ($indexType == 'data-write') {
			$indexType = 'data';
			$writeMode = true;
		}

		switch ($prefs['unified_engine']) {
		case 'lucene':
			ZendSearch\Lucene\Lucene::setTermsPerQueryLimit($prefs['unified_lucene_terms_limit']);
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

			$connection = $this->getElasticConnection($writeMode);
			$index = new Search_Elastic_Index($connection, $index);
			$index->setCamelCaseEnabled($prefs['unified_elastic_camel_case'] == 'y');
			$index->setFacetCount($prefs['search_facet_default_amount']);
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
		if($tiki_p_admin != 'y') {
			$errlib->report(tr('Contact the site administrator. The index needs rebuilding.'));
		} else {
			$errlib->report('<a title="' . tr("Rebuild Search index") .'" href="tiki-admin.php?page=search&rebuild=now">'. tr("Click here to rebuild index") . '</a>');
		}


		return new Search_Index_Memory;
	}

	function getEngineInfo()
	{
		global $prefs;

		switch ($prefs['unified_engine']) {
		case 'elastic':
			$info = array();

			try {
				$connection = $this->getElasticConnection(true);
				$root = $connection->rawApi('');
				$info[tr('Client Node')] = $root->name;
				$info[tr('ElasticSearch Version')] = $root->version->number;
				$info[tr('Lucene Version')] = $root->version->lucene_version;

				$cluster = $connection->rawApi('/_cluster/health');
				$info[tr('Cluster Name')] = $cluster->cluster_name;
				$info[tr('Cluster Status')] = $cluster->status;
				$info[tr('Cluster Node Count')] = $cluster->number_of_nodes;

				if (version_compare($root->version->number, '1.0.0') === -1) {
					$status = $connection->rawApi('/_status');
					foreach ($status->indices as $indexName => $data) {
						if (strpos($indexName, $prefs['unified_elastic_index_prefix']) === 0) {
							$info[tr('Index %0', $indexName)] = tr('%0 documents, totaling %1', 
								$data->docs->num_docs, $data->index->primary_size);
						}
					}

					$nodes = $connection->rawApi('/_nodes/jvm/stats');
					foreach ($nodes->nodes as $node) {
						$info[tr('Node %0', $node->name)] = tr('Using %0, since %1', $node->jvm->mem->heap_used, $node->jvm->uptime);
					}
				} else {
					$status = $connection->getIndexStatus();

					foreach ($status->indices as $indexName => $data) {
						if (strpos($indexName, $prefs['unified_elastic_index_prefix']) === 0) {
							if (isset($data->primaries)) {	// v2
								$info[tr('Index %0', $indexName)] = tr('%0 documents, totaling %1 bytes',
									$data->primaries->docs->count, number_format($data->primaries->store->size_in_bytes));
							} else {					// v1
								$info[tr('Index %0', $indexName)] = tr('%0 documents, totaling %1 bytes',
									$data->docs->num_docs, number_format($data->index->primary_size_in_bytes));
							}
						}
					}

					$nodes = $connection->rawApi('/_nodes/stats');
					foreach ($nodes->nodes as $node) {
						$info[tr('Node %0', $node->name)] = tr('Using %0 bytes, since %1', number_format($node->jvm->mem->heap_used_in_bytes), date('Y-m-d H:i:s', $node->jvm->timestamp / 1000));
					}
				}
			} catch (Search_Elastic_Exception $e) {
				$info[tr('Information Missing')] = $e->getMessage();
			}

			return $info;
		default:
			return array();
		}
	}

	public function getElasticIndexInfo($indexName)
	{
		$connection = $this->getElasticConnection(false);

		try {
			$mapping = $connection->rawApi("/$indexName/_mapping");

			return $mapping;
		} catch (Search_Elastic_Exception $e) {
			return false;
		}
	}

	private function getElasticConnection($useMasterOnly)
	{
		global $prefs;
		static $connections = [];

		$target = $prefs['unified_elastic_url'];

		if (! $useMasterOnly && $prefs['federated_elastic_url']) {
			$target = $prefs['federated_elastic_url'];
		}

		if (! empty($connections[$target])) {
			return $connections[$target];
		}

		$connection = new Search_Elastic_Connection($target);
		$connection->startBulk();
		$connection->persistDirty(TikiLib::events());

		$connections[$target] = $connection;
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
								return true;
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
		$this->initQueryBase($query);
		$this->initQueryPermissions($query);
		$this->initQueryPresentation($query);
	}

	function initQueryBase($query, $applyJail = true)
	{
		global $prefs;

		$query->setWeightCalculator($this->getWeightCalculator());
		$query->setIdentifierFields($prefs['unified_identifier_fields']);

		$categlib = TikiLib::lib('categ');
		if ($applyJail && $jail = $categlib->get_jail()) {
			$query->filterCategory(implode(' or ', $jail), true);
		}
	}
	
	function initQueryPermissions($query)
	{
		global $user;

		if (! Perms::get()->admin) {
			$query->filterPermissions(Perms::get()->getGroups(), $user);
		}
	}

	function initQueryPresentation($query)
	{
		$query->applyTransform(new Search_Formatter_Transform_DynamicLoader($this->getDataSource('formatting')));
	}

    /**
     * @param array $filter
     * @return Search_Query
     */
    function buildQuery(array $filter, $query = null)
	{
		if (! $query) {
			$query = new Search_Query;
			$this->initQuery($query);
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

		if (isset($filter['groups'])) {
			$query->filterMultivalue($filter['groups'], 'groups');
		}

		if (isset($filter['prefix']) && is_array($filter['prefix'])) {
			foreach ($filter['prefix'] as $field => $prefix) {
				$query->filterInitial((string) $prefix, $field);
			}

			unset($filter['prefix']);
		}

		if (isset($filter['not_prefix']) && is_array($filter['not_prefix'])) {
			foreach ($filter['not_prefix'] as $field => $prefix) {
				$query->filterNotInitial((string) $prefix, $field);
			}

			unset($filter['not_prefix']);
		}

		unset($filter['type']);
		unset($filter['categories']);
		unset($filter['deep']);
		unset($filter['tags']);
		unset($filter['content']);
		unset($filter['language']);
		unset($filter['language_unspecified']);
		unset($filter['autocomplete']);
		unset($filter['groups']);

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
				->setRenderMap(TikiLib::lib('language')->get_language_map());
		}

		$provider = new Search_FacetProvider;
		$provider->addFacets($facets);
		$this->addSources($provider);

		return $provider;
	}

	function getRawArray($document)
	{
		return array_map(function ($entry) {
			if (is_object($entry)) {
				if (method_exists($entry, 'getRawValue')) {
					return $entry->getRawValue();
				} else {
					return $entry->getValue();
				}
			} else {
				return $entry;
			}
		}, $document);
	}
}


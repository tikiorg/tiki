<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Index implements Search_Index_Interface, Search_Index_QueryRepository
{
	private $connection;
	private $index;
	private $facetCount = 10;
	private $invalidateList = array();

	private $providedMappings = array();

	private $camelCase = false;

	function __construct(Search_Elastic_Connection $connection, $index)
	{
		$this->connection = $connection;
		$this->index = $index;
	}

	function setCamelCaseEnabled($enabled)
	{
		$this->camelCase = (bool) $enabled;
	}

	function destroy()
	{
		$this->connection->deleteIndex($this->index);
		return true;
	}

	function exists()
	{
		$indexStatus = $this->connection->getIndexStatus($this->index);

		if (is_object($indexStatus)) {
			return !empty($indexStatus->indices->{$this->index});
		} else {
			return (bool) $indexStatus;
		}
	}

	function addDocument(array $data)
	{
		list($objectType, $objectId, $data) = $this->generateDocument($data);
		unset($this->invalidateList[$objectType . ':' . $objectId]);

		if (! empty($data['hash'])) {
			$objectId .= "~~{$data['hash']}";
		}

		$this->connection->index($this->index, $objectType, $objectId, $data);
	}

	private function generateDocument(array $data)
	{
		$objectType = $data['object_type']->getValue();
		$objectId = $data['object_id']->getValue();

		$this->generateMapping($objectType, $data);

		$data = array_map(
			function ($entry) {
				return $entry->getValue();
			}, $data
		);

		return [ $objectType, $objectId, $data ];
	}

	private function generateMapping($type, $data)
	{
		if (! isset($this->providedMappings[$type])) {
			$this->providedMappings[$type] = array();
		}

		$mapping = array_map(
			function ($entry) {
				if ($entry instanceof Search_Type_Numeric) {
					return array(
						"type" => "float",
						"fields" => array(
							"sort" => array(
								"type" => "float",
								"null_value" => 0.0,
								"ignore_malformed" => true,
							),
							"nsort" => array(
								"type" => "float",
								"null_value" => 0.0,
								"ignore_malformed" => true,
							),
						),
					);
				} elseif ($entry instanceof Search_Type_Whole || $entry instanceof Search_Type_MultivaluePlain) {
					return array(
						"type" => "string",
						"index" => "not_analyzed",
						"fields" => array(
							"sort" => array(
								"type" => "string",
								"analyzer" => "sortable",
							),
							"nsort" => array(
								"type" => "float",
								"null_value" => 0.0,
								"ignore_malformed" => true,
							),
						),
					);
				} elseif ($entry instanceof Search_Type_DateTime) {
					return array(
						"type" => "date",
						"fields" => array(
							"sort" => array(
								"type" => "date",
							),
							"nsort" => array(
								"type" => "date",
							),
						),
					);
				} else {
					return array(
						"type" => "string",
						"term_vector" => "with_positions_offsets",
						"fields" => array(
							"sort" => array(
								"type" => "string",
								"analyzer" => "sortable",
								"ignore_above" => 200,
							),
							"nsort" => array(
								"type" => "float",
								"null_value" => 0.0,
								"ignore_malformed" => true,
							),
						),
					);
				}
			}, array_diff_key($data, $this->providedMappings[$type])
		);
		$this->providedMappings[$type] = array_merge($this->providedMappings[$type], $mapping);
		$mapping = array_filter($mapping);

		if (! empty($mapping)) {
			$this->connection->mapping($this->index, $type, $mapping, function () {
				return $this->getIndexDefinition();
			});
		}
	}

	private function getIndexDefinition()
	{
		global $prefs;
		return [
			'analysis' => [
				'tokenizer' => [
					'camel' => [
						"type" => "pattern",
						"pattern" => "([^\\p{L}\\d]+)|(?<=\\D)(?=\\d)|(?<=\\d)(?=\\D)|(?<=[\\p{L}&&[^\\p{Lu}]])(?=\\p{Lu})|(?<=\\p{Lu})(?=\\p{Lu}[\\p{L}&&[^\\p{Lu}]])"
					],
				],
				'analyzer' => [
					'default' => [
						'tokenizer' => $this->camelCase ? 'camel' : 'standard',
						'filter' => ['standard', 'lowercase', 'asciifolding', 'tiki_stop', 'porterStem'],
					],
					'sortable' => [
						'tokenizer' => 'keyword',
						'filter' => ['lowercase', 'asciifolding'],
					],
				],
				'filter' => [
					'tiki_stop' => [
						'type' => 'stop',
						'stopwords' => $prefs['unified_stopwords'],
					],
				],
			],
		];
	}

	function endUpdate()
	{
		foreach ($this->invalidateList as $object) {
			$this->connection->unindex($this->index, $object['object_type'], $object['object_id']);
		}

		$this->connection->flush();

		$this->invalidateList = array();
	}

	function optimize()
	{
	}

	function invalidateMultiple(array $objectList)
	{
		foreach ($objectList as $object) {
			$key = $object['object_type'] . ':' . $object['object_id'];
			$this->invalidateList[$key] = $object;
		}
	}

	function find(Search_Query_Interface $query, $resultStart, $resultCount)
	{
		global $prefs;
		/**
		 * Sorted search size adjustment (part 1) - This is used to trim large data sets that need
		 * to be sorted by date. Sorting can take a very long time on these data sets and the data
		 * past a certain number of results are not generally useful.
		 *
		 * This checks to see if a particular query is cached if we are trying to sort by modification date.
		 * If cached, set a time range filter to minimize results.
		 */
		$soField = $query->getSortOrder()->getField();
		if ($prefs['unified_trim_sorted_search'] == 'y') {
			$cacheLib = TikiLib::lib("cache");
			$cacheKey = ($query->getExpr()->getSerializedParts());
			// if the sort order is modified or creation date, and there is a trim query cache item,
			// fetch the period filter that it should be filtering by (set in part 2, below) and add it to the search
			if (($soField == "modification_date" || $soField="creation_date") && $periodFilter = $cacheLib->getCached($cacheKey,"esquery")) {
				$query->filterRange(strtotime("-".$periodFilter." days"),time(),$soField);
			}
		}
		/**End of Sorted Search size adjustment (part 1)*/

		$builder = new Search_Elastic_OrderBuilder;
		$orderPart = $builder->build($query->getSortOrder());

		$builder = new Search_Elastic_FacetBuilder($this->facetCount);
		$facetPart = $builder->build($query->getFacets());

		$builder = new Search_Elastic_RescoreQueryBuilder;
		$rescorePart = $builder->build($query->getExpr());

		$builder = new Search_Elastic_QueryBuilder;
		$builder->setDocumentReader($this->createDocumentReader());
		$queryPart = $builder->build($query->getExpr());

		$postFilterPart = $builder->build($query->getPostFilter()->getExpr());
		if (empty($postFilterPart)) {
			$postFilterPart = [];
		} else {
			$postFilterPart = ["post_filter" => [
				'fquery' => $postFilterPart,
			]];
		}

		$indices = [$this->index];

		$foreign = array_map(function ($query) use ($builder) {
			return $builder->build($query->getExpr());
		}, $query->getForeignQueries());

		foreach ($foreign as $indexName => $foreignQuery) {
			$indices[] = $indexName;
			$queryPart = ['query' => [
				'indices' => [
					'index' => $indexName,
					'query' => $foreignQuery['query'],
					'no_match_query' => $queryPart['query'],
				],
			]];
		}

		$fullQuery = array_merge(
			$queryPart,
			$orderPart,
			$facetPart,
			$rescorePart,
			$postFilterPart,
			array(
				"from" => $resultStart,
				"size" => $resultCount,
				"highlight" => array(
					"tags_schema" => "styled",
					"fields" => array(
						'contents' => array(
							"number_of_fragments" => 5,
						),
						'file' => array(
							"number_of_fragments" => 5,
						),
					),
				),
			)
		);

		$result = $this->connection->search($indices, $fullQuery);
		$hits = $result->hits;

		/**
		 * Sorted Search size adjustment (part 2) - Checks to see if the number of results returned
		 * are more than 500. If they are, set an approximate period filter to get to 500 results next
		 * time that query is run.
		 */
		if ($prefs['unified_trim_sorted_search'] == 'y') {
			if ($hits->total >= 500) {
				if (empty($periodFilter)) {
					//set the default filter to ~6 months if no filter was previosuly set
					$periodFilter = 180;
				} else {
					// estimate the filter required to get 500 results
					$periodFilter = round(500/$hits->total * $periodFilter);
				}
				$cacheLib->cacheItem($cacheKey,(string) $periodFilter,"esquery");
			}
			// if total hits was less than 300 and a period filter had been set, increase the period filter
			// for the next search
			if ($hits->total < 300 && !empty($periodFilter)) {
				$periodFilter = round(300/$hits->total * $periodFilter);
				$cacheLib->cacheItem($cacheKey,(string) $periodFilter,"esquery");
			}
		}
		/** End Sorted Search size adjustment (part 2) */

		$indicesMap = array_combine($indices, $indices);

		$entries = array_map(
			function ($entry) use (& $indicesMap) {
				$data = (array) $entry->_source;

				if (isset($entry->highlight->contents)) {
					$data['_highlight'] = implode('...', $entry->highlight->contents);
				} elseif (isset($entry->highlight->file)) {
					$data['_highlight'] = implode('...', $entry->highlight->file);
				} else {
					$data['_highlight'] = '';
				}
				$data['score'] = round($entry->_score, 2);

				$index = $entry->_index;

				// Make sure we reduce the returned index to something matching what we requested
				// if what was requested is an alias.
				// Note: This only supports aliases where the name is a prefix.
				if (isset($indicesMap[$index])) {
					$index = $indicesMap[$index];
				} else {
					foreach ($indicesMap as $candidate) {
						if (0 === strpos($index, $candidate . '_')) {
							$indicesMap[$index] = $candidate;
							$index = $candidate;
							break;
						}
					}
				}

				$data['_index'] = $index;
				return $data;
			}, $hits->hits
		);

		$resultSet = new Search_Elastic_ResultSet($entries, $hits->total, $resultStart, $resultCount);

		$reader = new Search_Elastic_FacetReader($result);
		foreach ($query->getFacets() as $facet) {
			if ($filter = $reader->getFacetFilter($facet)) {
				$resultSet->addFacetFilter($filter);
			}
		}

		return $resultSet;
	}

	function scroll(Search_Query_Interface $query)
	{
		$builder = new Search_Elastic_OrderBuilder;
		$orderPart = $builder->build($query->getSortOrder());

		$builder = new Search_Elastic_QueryBuilder;
		$builder->setDocumentReader($this->createDocumentReader());
		$queryPart = $builder->build($query->getExpr());

		$indices = [$this->index];

		$fullQuery = array_merge(
			$queryPart,
			$orderPart,
			array(
				"size" => 100,
				"highlight" => array(
					"fields" => array(
						'contents' => array(
							"number_of_fragments" => 5,
						),
						'file' => array(
							"number_of_fragments" => 5,
						),
					),
				),
			)
		);

		$args = ['scroll' => '5m'];
		$result = $this->connection->search($indices, $fullQuery, $args);
		$scrollId = $result->_scroll_id;

		do {
			foreach ($result->hits->hits as $entry) {
				yield (array) $entry->_source;
			}

			$result = $this->connection->scroll($scrollId, $args);
		} while(count($result->hits->hits) > 0);
	}

	function getTypeFactory()
	{
		return new Search_Elastic_TypeFactory;
	}

	private function createDocumentReader()
	{
		$connection = $this->connection;
		$index = $this->index;
		return function ($type, $object) use ($connection, $index) {
			static $previous, $content;

			$now = "$index~$type~$object";
			if ($previous === $now) {
				return $content;
			}

			$previous = $now;
			$content = (array) $connection->document($index, $type, $object);
			return $content;
		};
	}

	function getMatchingQueries(array $document)
	{
		list($type, $object, $document) = $this->generateDocument($document);
		$result = $this->connection->percolate($this->index, $type, $document);
		return array_map(function ($item) {
			return $item->_id;
		}, $result->matches);
	}

	function store($name, Search_Expr_Interface $expr)
	{
		$builder = new Search_Elastic_QueryBuilder;
		$builder->setDocumentReader($this->createDocumentReader());
		$doc = $builder->build($expr);

		$this->connection->storeQuery($this->index, $name, $doc);
	}

	function unstore($name)
	{
		$this->connection->unstoreQuery($this->index, $name);
	}

	function setFacetCount($count)
	{
		$this->facetCount = (int) $count;
	}
}


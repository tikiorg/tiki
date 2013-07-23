<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Index implements Search_Index_Interface
{
	private $connection;
	private $index;
	private $invalidateList = array();

	private $providedMappings = array();

	function __construct(Search_Elastic_Connection $connection, $index)
	{
		$this->connection = $connection;
		$this->index = $index;
	}

	function destroy()
	{
		$this->connection->deleteIndex($this->index);
		return true;
	}

	function exists()
	{
		return (bool) $this->connection->getIndexStatus($this->index);
	}

	function addDocument(array $data)
	{
		$objectType = $data['object_type']->getValue();
		$objectId = $data['object_id']->getValue();

		$this->generateMapping($objectType, $data);

		$data = array_map(
			function ($entry) {
				return $entry->getValue();
			}, $data
		);

		if (! empty($data['hash'])) {
			$objectId .= "~~{$data['hash']}";
		}

		unset($this->invalidateList[$objectType . ':' . $objectId]);

		$this->connection->index($this->index, $objectType, $objectId, $data);
	}

	private function generateMapping($type, $data)
	{
		if (! isset($this->providedMappings[$type])) {
			$this->providedMappings[$type] = array();
		}

		$mapping = array_map(
			function ($entry) {
				if ($entry instanceof Search_Type_Whole || $entry instanceof Search_Type_MultivaluePlain) {
					return array(
						"type" => "string",
						"index" => "not_analyzed",
					);
				}
			}, array_diff_key($data, $this->providedMappings[$type])
		);
		$this->providedMappings[$type] = array_merge($this->providedMappings[$type], $mapping);
		$mapping = array_filter($mapping);

		if (! empty($mapping)) {
			$this->connection->mapping($this->index, $type, $mapping);
		}
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
		$builder = new Search_Elastic_QueryBuilder;
		$builder->setDocumentReader($this->createDocumentReader());
		$queryPart = $builder->build($query->getExpr());

		$builder = new Search_Elastic_OrderBuilder;
		$orderPart = $builder->build($query->getSortOrder());

		$builder = new Search_Elastic_FacetBuilder;
		$facetPart = $builder->build($query->getFacets());

		$fullQuery = array_merge(
			$queryPart,
			$orderPart,
			$facetPart,
			array(
				"from" => $resultStart,
				"size" => $resultCount,
				"highlight" => array(
					"fields" => array(
						'contents' => array(
							"number_of_fragments" => 5,
						),
					),
				),
			)
		);

		$result = $this->connection->search($this->index, $fullQuery, $resultStart, $resultCount);
		$hits = $result->hits;

		$entries = array_map(
			function ($entry) {
				$data = (array) $entry->_source;

				if (isset($entry->highlight->contents)) {
					$data['_highlight'] = implode('...', $entry->highlight->contents);
				} else {
					$data['_highlight'] = '';
				}

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

	function getTypeFactory()
	{
		return new Search_Elastic_TypeFactory;
	}

	private function createDocumentReader()
	{
		$connection = $this->connection;
		$index = $this->index;
		return function ($type, $object) use ($connection, $index) {
			return (array) $connection->document($index, $type, $object);
		};
	}
}


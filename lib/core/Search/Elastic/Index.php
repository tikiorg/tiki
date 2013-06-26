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
		$factory = $this->getTypeFactory();
		$objectType = $data['object_type']->getValue($factory);
		$objectId = $data['object_id']->getValue($factory);

		$this->generateMapping($objectType, $data);

		$factory = $this->getTypeFactory();
		$data = array_map(
			function ($entry) use ($factory) {
				return $entry->getValue($factory);
			}, $data
		);
		$objectType = $data['object_type'];
		$objectId = $data['object_id'];

		if (! empty($data['hash'])) {
			$objectId .= "~~{$data['hash']}";
		}

		unset($this->invalidateList[$objectType . ':' . $objectId]);

		$this->connection->index($this->index, $objectType, $objectId, $data);
	}

	private function generateMapping($type, $data)
	{
		if (isset($this->providedMappings[$type])) {
			return;
		}

		$this->providedMappings[$type] = true;

		$mapping = array_map(
			function ($entry) {
				if ($entry instanceof Search_Type_Whole || $entry instanceof Search_Type_MultivaluePlain) {
					return array(
						"type" => "string",
						"index" => "not_analyzed",
					);
				}
			}, $data
		);
		$mapping = array_filter($mapping);

		$this->connection->mapping($this->index, $type, $mapping);
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

	private function buildQuery($expr)
	{
		$query = $expr->walk(array($this, 'walkCallback'));
		return $query;
	}

	function walkCallback($node, $childNodes)
	{
		$term = null;

		if ($node instanceof Search_Expr_And) {
			$term = $this->buildCondition($childNodes, true);
		} elseif ($node instanceof Search_Expr_Or) {
			$term = $this->buildCondition($childNodes, null);
		} elseif ($node instanceof Search_Expr_Not) {
			$result = new Zend_Search_Lucene_Search_Query_Boolean;
			$result->addSubquery($childNodes[0], false);

			$term = $result;
		} elseif ($node instanceof Search_Expr_Range) {
			$from = $node->getToken('from');
			$to = $node->getToken('to');

			$from = $this->buildTerm($from);
			$to = $this->buildTerm($to);

			// Range search not supported for phrases, so revert to normal token matching
			if (method_exists($from, 'getTerm')) {
				$range = new Zend_Search_Lucene_Search_Query_Range(
					$from->getTerm(),
					$to->getTerm(),
					true // inclusive
				);

				$term = $range;
			} else {
				$term = $from;
			}
		} elseif ($node instanceof Search_Expr_Token) {
			$term = $this->buildTerm($node);
		}

		if ($term && method_exists($term, 'getTerm') && (string) $term->getTerm()->text) {
			$term->setBoost($node->getWeight());
		}

		return $term;
	}

	private function buildCondition($childNodes, $required)
	{
		$result = new Zend_Search_Lucene_Search_Query_Boolean;
		foreach ($childNodes as $child) {

			// Detect if child is a NOT, and reformulate on the fly to support the syntax
			if ($child instanceof Zend_Search_Lucene_Search_Query_Boolean) {
				$signs = $child->getSigns();
				if (count($signs) === 1 && $signs[0] === false) {
					$result->addSubquery(reset($child->getSubqueries()), false);
					continue;
				}
			}

			$result->addSubquery($child, $required);
		}

		return $result;
	}

	private function buildTerm($node)
	{
		$value = $node->getValue($this->getTypeFactory());
		$field = $node->getField();

		switch (get_class($value)) {
		case 'Search_Type_WikiText':
		case 'Search_Type_PlainText':
		case 'Search_Type_MultivalueText':
			$whole = $value->getValue();
			$whole = str_replace(array('*', '?', '~', '+'), '', $whole);
			$whole = str_replace(array('[', ']', '{', '}', '(', ')', ':', '-'), ' ', $whole);

			$parts = explode(' ', $whole);
			if (count($parts) === 1) {
				return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($parts[0], $field), true);
			} else {
				return new Zend_Search_Lucene_Search_Query_Phrase($parts, array_keys($parts), $field);
			}
		case 'Search_Type_Timestamp':
			$parts = explode(' ', $value->getValue());
			return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($parts[0], $field), true);
		case 'Search_Type_Whole':
			$parts = explode(' ', $value->getValue());
			return new Zend_Search_Lucene_Search_Query_Phrase($parts, array_keys($parts), $field);
		}
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


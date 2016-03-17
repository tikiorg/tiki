<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query implements Search_Query_Interface
{
	private $objectList;
	private $expr;
	private $sortOrder;
	private $start = 0;
	private $count = 50;
	private $weightCalculator = null;
	private $identifierFields = null;

	private $postFilter;
	private $subQueries = array();
	private $facets = [];
	private $foreignQueries = [];
	private $transformations = [];

	function __construct($query = null)
	{
		$this->expr = new Search_Expr_And(array());

		if ($query) {
			$this->filterContent($query);
		}
	}

	function __clone()
	{
		$this->expr = clone $this->expr;
	}

	function setIdentifierFields(array $fields)
	{
		$this->identifierFields = $fields;
	}

	function addObject($type, $objectId)
	{
		if (is_null($this->objectList)) {
			$this->objectList = new Search_Expr_Or(array());
			$this->expr->addPart($this->objectList);
		}

		$type = new Search_Expr_Token($type, 'identifier', 'object_type');
		$objectId = new Search_Expr_Token($objectId, 'identifier', 'object_id');

		$this->objectList->addPart(new Search_Expr_And(array($type, $objectId)));
	}

	function filterContent($query, $field = 'contents')
	{
		$this->addPart($query, 'plaintext', $field);
	}

	function filterIdentifier($query, $field)
	{
		$this->addPart(new Search_Expr_Token($query), 'identifier', $field);
	}

	function filterType($types)
	{
		if (is_array($types)) {
			foreach ($types as $type) {
				if ($type) {
					$tokens[] = new Search_Expr_Token($type);
				}
			}
			if (isset($tokens)) {
				$or =  new Search_Expr_Or($tokens);
				$this->addPart($or, 'identifier', 'object_type');
			}
		} elseif ($types) {
			$token = new Search_Expr_Token($types);
			$this->addPart($token, 'identifier', 'object_type');
		}
	}

	function filterMultivalue($query, $field)
	{
		$this->addPart($query, 'multivalue', $field);
	}

	function filterContributors($query)
	{
		$this->filterMultivalue($query, 'contributors');
	}

	function filterCategory($query, $deep = false)
	{
		$this->filterMultivalue($query, $deep ? 'deep_categories' : 'categories');
	}

	function filterTags($query)
	{
		$this->filterMultivalue($query, 'freetags');
	}

	function filterLanguage($query)
	{
		$this->addPart($query, 'identifier', 'language');
	}

	function filterPermissions(array $groups, $user = null)
	{
		$tokens = array();
		foreach ($groups as $group) {
			$tokens[] = new Search_Expr_Token($group);
		}

		$or = new Search_Expr_Or($tokens);

		if ($user) {
			$sub = $this->getSubQuery('permissions');
			$sub->filterMultivalue($or, 'allowed_groups');
			$sub->filterMultivalue(new Search_Expr_Token($user), 'allowed_users');
		} else {
			$this->addPart($or, 'multivalue', 'allowed_groups');
		}
	}

	/**
	 * Sets up Zend search term for a date range
	 *
	 * @param string	$from date - a unix timestamp or most date strings such as 'now', '2011-11-21', 'last week' etc
	 * @param string	$to date as with $from (other examples: '-42 days', 'last tuesday')
	 * @param string	$field to search in such as 'tracker_field_42'. default: modification_date
	 * @link			http://www.php.net/manual/en/datetime.formats.php
	 * @return void
	 */

	function filterRange($from, $to, $field = 'modification_date')
	{
		if (!is_numeric($from)) {
			$from2 = strtotime($from);
			if ($from2) {
				$from = $from2;
			} else {
				TikiLib::lib('errorreport')->report(tra('filterRange: "from" value not parsed'));
			}
		}
		if (!is_numeric($to)) {
			$to2 = strtotime($to);
			if ($to2) {
				$to = $to2;
			} else {
				TikiLib::lib('errorreport')->report(tra('filterRange: "to" value not parsed'));
			}
		}

		$this->addPart(new Search_Expr_Range($from, $to), 'timestamp', $field);
	}

	function filterTextRange($from, $to, $field = 'title')
	{
		$this->addPart(new Search_Expr_Range($from, $to), 'plaintext', $field);
	}

	function filterInitial($initial, $field = 'title')
	{
		$this->addPart(new Search_Expr_Initial($initial), 'plaintext', $field);
	}

	function filterNotInitial($initial, $field = 'title')
	{
		$this->addPart(new Search_Expr_Not(new Search_Expr_Initial($initial)), 'plaintext', $field);
	}

	function filterRelation($query, array $invertable = array())
	{
		$query = $this->parse($query);
		$replacer = new Search_Query_RelationReplacer($invertable);
		$query = $query->walk(array($replacer, 'visit'));
		$this->addPart($query, 'multivalue', 'relations');
	}

	function filterSimilar($type, $object, $field = 'contents')
	{
		$part = new Search_Expr_And(
			array(
				new Search_Expr_Not(
					new Search_Expr_And(
						array(
							new Search_Expr_Token($type, 'identifier', 'object_type'),
							new Search_Expr_Token($object, 'identifier', 'object_id'),
						)
					)
				),
				$mlt = new Search_Expr_MoreLikeThis($type, $object),
			)
		);
		$mlt->setField($field);
		$this->expr->addPart($part);
	}

	function filterSimilarToThese($objects, $content, $field = 'contents')
	{
		$excluded = [];
		foreach ($objects as $object) {
			$excluded[] = new Search_Expr_And(
				array(
					new Search_Expr_Token($object['object_type'], 'identifier', 'object_type'),
					new Search_Expr_Token($object['object_id'], 'identifier', 'object_id'),
				)
			);
		}

		$mlt = new Search_Expr_MoreLikeThis($content);
		$mlt->setField($field);

		$part = new Search_Expr_And(
			array(
				$mlt,
				new Search_Expr_Not(new Search_Expr_Or($excluded)),
			)
		);
		$this->expr->addPart($part);
	}

	private function addPart($query, $type, $field)
	{
		if (is_string($field)) {
			$field = explode(',', $field);
		}

		$parts = array();
		foreach ((array) $field as $f) {
			$part = $this->parse($query);
			$part->setType($type);
			$part->setField($f);
			$parts[] = $part;
		}

		if (count($parts) === 1) {
			$this->expr->addPart($parts[0]);
		} else {
			$this->expr->addPart(new Search_Expr_Or($parts));
		}
	}

	function setOrder($order)
	{
		if (is_string($order)) {
			$this->sortOrder = Search_Query_Order::parse($order);
		} else {
			$this->sortOrder = $order;
		}
	}

	function setRange($start, $count = null)
	{
		$this->start = (int) $start;

		if ($count) {
			$this->count = (int) $count;
		}
	}

	function setCount($count = null)
	{
		if ($count) {
			$this->count = (int) $count;
		}
	}

	/**
	 * Affects the range from a numeric value
	 * @param $pageNumber int Page number from 1 to n
	 */
	function setPage($pageNumber)
	{
		$pageNumber = max(1, (int) $pageNumber);
		$this->setRange(($pageNumber - 1) * $this->count);
	}

	function setWeightCalculator(Search_Query_WeightCalculator_Interface $calculator)
	{
		$this->weightCalculator = $calculator;
	}

	function getSortOrder()
	{
		if ($this->sortOrder) {
			return $this->sortOrder;
		} else {
			return Search_Query_Order::getDefault();
		}
	}

	function search(Search_Index_Interface $index)
	{
		$this->finalize();

		try {
			$resultset = $index->find($this, $this->start, $this->count);
		} catch(Search_Elastic_SortException $e) {
			//on sort exception, try again without the sort field
			$this->sortOrder = null;
			$resultset = $index->find($this, $this->start, $this->count);
		} catch(Exception $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
			return Search_ResultSet::create([]);
		}

		$resultset->applyTransform(function ($entry) {
			if (! isset($entry['_index']) || ! isset($this->foreignQueries[$entry['_index']])) {
				foreach ($this->transformations as $trans) {
					$entry = $trans($entry);
				}
			}

			return $entry;
		});

		foreach ($this->foreignQueries as $indexName => $query) {
			$resultset->applyTransform(function ($entry) use ($query, $indexName) {
				if (isset($entry['_index']) && $entry['_index'] == $indexName) {
					foreach ($query->transformations as $trans) {
						$entry = $trans($entry);
					}
				}

				return $entry;
			});
		}

		return $resultset;
	}

	function scroll($index)
	{
		$this->finalize();
		$res = $index->scroll($this);

		foreach ($res as $row) {
			foreach ($this->transformations as $trans) {
				$row = $trans($row);
			}

			yield $row;
		}
	}

	function applyTransform(callable $transform)
	{
		$this->transformations[] = $transform;
	}

	function store($name, $index)
	{
		if ($index instanceof Search_Index_QueryRepository) {
			$this->finalize();
			$index->store($name, $this->expr);
			return true;
		}

		return false;
	}

	private function finalize()
	{
		if ($this->weightCalculator) {
			$this->expr->walk([$this->weightCalculator, 'calculate']);

			if ($this->postFilter) {
				$this->postFilter->expr->walk([$this->weightCalculator, 'calculate']);
			}

			foreach ($this->foreignQueries as $query) {
				$query->expr->walk([$this->weightCalculator, 'calculate']);
			}
		}

		if ($this->identifierFields) {
			$fields = $this->identifierFields;
			$this->expr->walk(
				function (Search_Expr_Interface $expr) use ($fields) {
					if (method_exists($expr, 'getField') && in_array($expr->getField(), $fields)) {
						$expr->setType('identifier');
					}
				}
			);

			if ($this->postFilter) {
				$this->postFilter->expr->walk(
					function (Search_Expr_Interface $expr) use ($fields) {
						if (method_exists($expr, 'getField') && in_array($expr->getField(), $fields)) {
							$expr->setType('identifier');
						}
					}
				);
			}

			foreach ($this->foreignQueries as $query) {
				$query->expr->walk(
					function (Search_Expr_Interface $expr) use ($fields) {
						if (method_exists($expr, 'getField') && in_array($expr->getField(), $fields)) {
							$expr->setType('identifier');
						}
					}
				);
			}
		}
	}

	function getExpr()
	{
		return $this->expr;
	}

	private function parse($query)
	{
		if (is_string($query)) {
			$parser = new Search_Expr_Parser;
			$query = $parser->parse($query);
		} elseif ($query instanceof Search_Expr_Interface) {
			$query = clone $query;
		}

		return $query;
	}

	function getTerms()
	{
		$terms = array();

		$extractor = new Search_Type_Factory_Direct;

		$this->expr->walk(
			function ($expr) use (& $terms, $extractor) {
				if ($expr instanceof Search_Expr_Token && $expr->getField() == 'contents') {
					$terms[] = $expr->getValue($extractor)->getValue();
				}
			}
		);

		return $terms;
	}

	function getSubQuery($name)
	{
		if (empty($name)) {
			return $this;
		}

		if (! isset($this->subQueries[$name])) {
			$subquery = new self;
			$subquery->expr = new Search_Expr_Or(array());
			$this->expr->addPart($subquery->expr);

			$this->subQueries[$name] = $subquery;
		}

		return $this->subQueries[$name];
	}

	function getPostFilter()
	{
		if (! $this->postFilter) {
			$subquery = new self;
			$this->postFilter = $subquery;
			$subquery->postFilter = $subquery;
		}

		return $this->postFilter;
	}

	function requestFacet(Search_Query_Facet_Interface $facet)
	{
		$this->facets[] = $facet;
	}

	function getFacets()
	{
		return $this->facets;
	}

	function includeForeign($indexName, Search_Query $query)
	{
		$this->foreignQueries[$indexName] = $query;
	}

	function getForeignQueries()
	{
		return $this->foreignQueries;
	}
}

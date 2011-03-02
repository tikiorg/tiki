<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query
{
	private $objectList;
	private $expr;
	private $sortOrder;
	private $start = 0;
	private $count = 50;

	function __construct($query = null)
	{
		$this->expr = new Search_Expr_And(array());

		if ($query) {
			$this->filterContent($query);
		}
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

	function filterType($type)
	{
		$token = new Search_Expr_Token($type);
		$this->addPart($token, 'identifier', 'object_type');
	}

	function filterCategory($query, $deep = false)
	{
		$this->addPart($query, 'multivalue', $deep ? 'deep_categories' : 'categories');
	}

	function filterTags($query)
	{
		$this->addPart($query, 'multivalue', 'freetags');
	}

	function filterLanguage($query)
	{
		$this->addPart($query, 'identifier', 'language');
	}

	function filterPermissions(array $groups)
	{
		$tokens = array();
		foreach ($groups as $group) {
			$tokens[] = new Search_Expr_Token($group);
		}

		$or = new Search_Expr_Or($tokens);

		$this->addPart($or, 'multivalue', 'allowed_groups');
	}

	function filterRange($from, $to, $field = 'modification_date')
	{
		$this->expr->addPart(new Search_Expr_Range($from, $to, 'timestamp', $field));
	}

	private function addPart($query, $type, $field)
	{
		$query = $this->parse($query);
		$query->setType($type);
		$query->setField($field);
		$this->expr->addPart($query);
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

	function search(Search_Index_Interface $index)
	{
		if ($this->sortOrder) {
			$sortOrder = $this->sortOrder;
		} else {
			$sortOrder = Search_Query_Order::getDefault();
		}

		return $index->find($this->expr, $sortOrder, $this->start, $this->count);
	}

	function invalidate(Search_Index_Interface $index)
	{
		return $index->invalidateMultiple($this->expr);
	}
	
	private function parse($query)
	{
		if (is_string($query)) {
			$parser = new Search_Expr_Parser;
			$query = $parser->parse($query);
		}

		return $query;
	}
}

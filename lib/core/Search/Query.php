<?php

class Search_Query
{
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

	function filterContent($query, $field = 'global')
	{
		$this->addPart($query, 'wikitext', $field);
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
	
	private function parse($query)
	{
		if (is_string($query)) {
			$parser = new Search_Expr_Parser;
			$query = $parser->parse($query);
		}

		return $query;
	}
}

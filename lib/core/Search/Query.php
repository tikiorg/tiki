<?php

class Search_Query
{
	private $expr;

	function __construct($query = null)
	{
		$this->expr = new Search_Expr_And(array());

		if ($query) {
			$this->addTextCriteria($query);
		}
	}

	function addTextCriteria($query, $field = 'global')
	{
		$this->addPart($query, 'wikitext', $field);
	}

	function filterType($type)
	{
		$token = new Search_Expr_Token($type);
		$this->addPart($token, 'identifier', 'object_type');
	}

	function filterCategory($query)
	{
		$this->addPart($query, 'multivalue', 'categories');
	}

	function filterLanguage($query)
	{
		$this->addPart($query, 'identifier', 'language');
	}

	private function addPart($query, $type, $field)
	{
		$query = $this->parse($query);
		$query->setType($type);
		$query->setField($field);
		$this->expr->addPart($query);
	}

	function search(Search_Index_Interface $index)
	{
		return $index->find($this->expr);
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

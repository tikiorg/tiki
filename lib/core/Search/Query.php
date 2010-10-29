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
		$query = $this->parse($query);

		$query->setType('wikitext');
		$query->setField($field);
		$this->expr->addPart($query);
	}

	function filterType($type)
	{
		$token = new Search_Expr_Token($type);
		$token->setType('identifier');
		$token->setField('object_type');
		$this->expr->addPart($token);
	}

	function filterCategory($query)
	{
		$query = $this->parse($query);
		$query->setType('multivalue');
		$query->setField('categories');
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

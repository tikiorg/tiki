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
		if (is_string($query)) {
			$parser = new Search_Expr_Parser;
			$query = $parser->parse($query);
		}

		if ($query instanceof Search_Expr_Interface) {
			$query->setType('wikitext');
			$query->setField($field);
			$this->expr->addPart($query);
		}
	}

	function search(Search_Index_Interface $index)
	{
		return $index->find($this->expr);
	}
}

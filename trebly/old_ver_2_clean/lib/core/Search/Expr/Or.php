<?php

class Search_Expr_Or implements Search_Expr_Interface
{
	private $parts;

	function __construct(array $parts)
	{
		$this->parts = $parts;
	}

	function addPart(Search_Expr_Interface $part)
	{
		$this->parts[] = $part;
	}

	function setType($type)
	{
		foreach ($this->parts as $part) {
			$part->setType($type);
		}
	}

	function setField($field = 'global')
	{
		foreach ($this->parts as $part) {
			$part->setField($field);
		}
	}

	function walk($callback)
	{
		$results = array();
		foreach ($this->parts as $part) {
			$results[] = $part->walk($callback);
		}

		return call_user_func($callback, $this, $results);
	}
}


<?php

class Search_Expr_Not implements Search_Expr_Interface
{
	private $expression;

	function __construct($expression)
	{
		$this->expression = $expression;
	}

	function setType($type)
	{
		$this->expression->setType($type);
	}

	function setField($field = 'global')
	{
		$this->expression->setField($field);
	}

	function walk($callback)
	{
		$result = $this->expression->walk($callback);

		return call_user_func($callback, $this, array($result));
	}
}


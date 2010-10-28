<?php

class Search_Expr_Not implements Search_Expr_Interface
{
	private $expression;

	function __construct($expression)
	{
		$this->expression = $expression;
	}
}


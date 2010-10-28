<?php

class Search_Expr_Or implements Search_Expr_Interface
{
	private $parts;

	function __construct(array $parts)
	{
		$this->parts = $parts;
	}
}


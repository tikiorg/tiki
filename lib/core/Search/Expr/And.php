<?php

class Search_Expr_And implements Search_Expr_Interface
{
	private $parts;

	function __construct(array $parts)
	{
		$this->parts = $parts;
	}
}


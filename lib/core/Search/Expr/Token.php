<?php

class Search_Expr_Token implements Search_Expr_Interface
{
	private $string;

	function __construct($string)
	{
		$this->string = $string;
	}
}


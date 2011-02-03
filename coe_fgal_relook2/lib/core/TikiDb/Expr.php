<?php

class TikiDb_Expr
{
	private $string;
	private $arguments;

	function __construct($string, array $arguments)
	{
		$this->string = $string;
		$this->arguments = $arguments;
	}

	function getQueryPart($currentField)
	{
		return str_replace('$$', $currentField, $this->string);
	}

	function getValues()
	{
		return $this->arguments;
	}
}


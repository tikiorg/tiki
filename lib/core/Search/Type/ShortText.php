<?php

class Search_Type_ShortText implements Search_Type_Interface
{
	private $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	function getValue()
	{
		return strtolower($this->value);
	}
}

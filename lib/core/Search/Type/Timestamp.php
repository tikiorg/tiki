<?php

class Search_Type_Timestamp implements Search_Type_Interface
{
	private $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	function getValue()
	{
		return $this->value;
	}
}


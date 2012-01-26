<?php

class Search_Expr_Token implements Search_Expr_Interface
{
	private $string;
	private $type;
	private $field;

	function __construct($string, $type = null, $field = null)
	{
		$this->string = $string;
		$this->type = $type;
		$this->field = $field;
	}

	function setType($type)
	{
		$this->type = $type;
	}

	function setField($field = 'global')
	{
		$this->field = $field;
	}

	function walk($callback)
	{
		return call_user_func($callback, $this, array());
	}

	function getValue(Search_Type_Factory_Interface $typeFactory)
	{
		$type = $this->type;
		return $typeFactory->$type($this->string);
	}

	function getField()
	{
		return $this->field;
	}
}


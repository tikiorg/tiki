<?php

class Search_Type_WikiText implements Search_Type_Interface
{
	private $value;

	function __construct($value)
	{
		$this->value = $value;
	}

	function getValue()
	{
		global $tikilib;
		$out = $tikilib->parse_data($this->value, array(
			'parsetoc' => false,
			'indexing' => true,
		));

		return $out;
	}
}


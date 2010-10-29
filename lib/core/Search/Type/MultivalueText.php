<?php

class Search_Type_MultivalueText implements Search_Type_Interface
{
	private $values;

	function __construct(array $values)
	{
		$this->values = $values;
	}

	function getValue()
	{
		$strings = array();
		foreach ($this->values as $val) {
			$strings[] = 'token' . $val;
		}

		return implode(' ', $strings);
	}
}


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
			$val = md5($val);
			$raw = 'token' . $val;

			// Must strip numbers to prevent tokenization
			$strings[] = strtr($raw, '1234567890', 'pqrtuvwxyz');
		}

		return implode(' ', $strings);
	}
}


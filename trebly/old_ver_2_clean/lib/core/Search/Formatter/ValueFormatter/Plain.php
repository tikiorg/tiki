<?php

class Search_Formatter_ValueFormatter_Plain implements Search_Formatter_ValueFormatter_Interface
{
	function render($value, array $entry)
	{
		return $value;
	}
}


<?php

class Search_Formatter_ValueFormatter_Date implements Search_Formatter_ValueFormatter_Interface
{
	function render($value, array $entry)
	{
		global $prefs, $tikilib;
		return $tikilib->date_format($prefs['short_date_format'], $value);
	}
}


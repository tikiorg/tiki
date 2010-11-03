<?php

class Search_Formatter_ValueFormatter_Date implements Search_Formatter_ValueFormatter_Interface
{
	function render($value)
	{
		global $prefs;
		return date($prefs['short_date_format'], $value);
	}
}


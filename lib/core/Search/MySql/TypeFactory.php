<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_TypeFactory implements Search_Type_Factory_Interface
{
	function plaintext($value)
	{
		return new Search_Type_PlainText($value);
	}

	function wikitext($value)
	{
		return new Search_Type_WikiText($value);
	}

	function timestamp($value)
	{
		if (is_numeric($value)) {
			return new Search_Type_Timestamp(gmdate('Y-m-d H:i:s', $value));
		} else {
			// if mysql sql_mode is set to NO_ZERO_IN_DATE or NO_ZERO_DATE then'0000-00-00 00:00:00' produces errors
			return new Search_Type_Timestamp(null);
		}

	}

	function identifier($value)
	{
		return new Search_Type_Whole($value);
	}

	function numeric($value)
	{
		return new Search_Type_Numeric($value);
	}

	function multivalue($values)
	{
		return new Search_Type_MultivalueText((array) $values);
	}

	function sortable($value)
	{
		return new Search_Type_PlainShortText($value);
	}
}


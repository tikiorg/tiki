<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Lucene_TypeFactory implements Search_Type_Factory_Interface
{
	function plaintext($value)
	{
		return new Search_Type_PlainText($value);
	}

	function wikitext($value)
	{
		return new Search_Type_WikiText($value);
	}

	function timestamp($value, $dateOnly = false)
	{
		if (is_numeric($value)) {
			return new Search_Type_Timestamp(gmdate('YmdHis', $value));
		} else {
			return new Search_Type_PlainText('');
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

	/* Not supported in Lucene indexes - use elasticsearch*/
	function object($values)
	{
		return null;
	}
	/* Not supported in Lucene indexes - use elasticsearch */
	function nested($values)
	{
		return null;
	}

	/* Not supported in Lucene indexes - use elasticsearch */
	function geopoint($values)
	{
		return null;
	}

	function sortable($value)
	{
		return new Search_Type_ShortText($value);
	}

	function json($value)
	{
		return new Search_Type_PlainText($value);
	}
}


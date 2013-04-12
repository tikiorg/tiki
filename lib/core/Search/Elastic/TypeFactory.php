<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_TypeFactory implements Search_Type_Factory_Interface
{
	function plaintext($value)
	{
		return new Search_Type_Whole($value);
	}

	function wikitext($value)
	{
		return new Search_Type_PlainText($value);
	}

	function timestamp($value)
	{
		if (is_numeric($value)) {
			return new Search_Type_Whole(gmdate(DateTime::W3C, $value));
		} else {
			return new Search_Type_Whole('');
		}
	}

	function identifier($value)
	{
		return new Search_Type_Whole($value);
	}

	function multivalue($values)
	{
		return new Search_Type_Whole((array) $values);
	}

	function sortable($value)
	{
		return new Search_Type_Whole($value);
	}
}


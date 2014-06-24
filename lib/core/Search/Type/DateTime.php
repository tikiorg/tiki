<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Type_DateTime extends Search_Type_PlainText
{
	function __construct($value)
	{
		if (is_numeric($value)) {
			parent::__construct(gmdate(DateTime::W3C, $value));
		} else {
			parent::__construct(null);
		}
	}
}

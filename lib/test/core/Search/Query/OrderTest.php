<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_OrderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider sortMatches
	 */
	function testParse($mode, $field, $order, $type)
	{
		$obtained = Search_Query_Order::parse($mode);
		$this->assertEquals(new Search_Query_Order($field, $type, $order), $obtained);
	}

	function sortMatches()
	{
		return [
			['', 'score', 'desc', 'numeric'],
			['title', 'title', 'asc', 'text'],
			['title_asc', 'title', 'asc', 'text'],
			['title_desc', 'title', 'desc', 'text'],
			['title_nasc', 'title', 'asc', 'numeric'],
			['title_ndesc', 'title', 'desc', 'numeric'],
			['modification_date_ndesc', 'modification_date', 'desc', 'numeric'],
		];
	}
}

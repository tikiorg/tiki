<?php

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
		return array(
			array('', 'score', 'desc', 'numeric'),
			array('title', 'title', 'asc', 'text'),
			array('title_asc', 'title', 'asc', 'text'),
			array('title_desc', 'title', 'desc', 'text'),
			array('title_nasc', 'title', 'asc', 'numeric'),
			array('title_ndesc', 'title', 'desc', 'numeric'),
			array('modification_date_ndesc', 'modification_date', 'desc', 'numeric'),
		);
	}
}


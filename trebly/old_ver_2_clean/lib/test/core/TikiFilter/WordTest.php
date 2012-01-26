<?php

/** 
 * @group unit
 * 
 */

class TikiFilter_WordTest extends TikiTestCase
{
	private $array;

	function testFilter()
	{
		$filter = new TikiFilter_Word();

		$this->assertEquals( '123ab_c', $filter->filter('-123 ab_c') );
	}
}

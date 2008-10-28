<?php

class JitFilter_WordTest extends PHPUnit_Framework_TestCase
{
	private $array;

	function testFilter()
	{
		$filter = new JitFilter_Word();

		$this->assertEquals( '123ab_c', $filter->filter('-123 ab_c') );
	}
}

?>

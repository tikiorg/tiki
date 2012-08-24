<?php

class TikiLib_MultiExplodeTest extends PHPUnit_Framework_TestCase
{
	function testSimple() {
		$lib = TikiLib::lib('tiki');
		$this->assertEquals(array('A', 'B'), $lib->multi_explode(':', 'A:B'));
		$this->assertEquals(array('A::B'), $lib->multi_explode(':', 'A::B'));
		$this->assertEquals(array('A:::B'), $lib->multi_explode(':', 'A:::B'));
	}
}


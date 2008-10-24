<?php

class JitFilter_XssTest extends PHPUnit_Framework_TestCase
{
	function testSimple()
	{
		$filter = new JitFilter_PreventXss;

		$this->assertEquals( '<a href="http://example.com" on<x>click="al<x>ert(\'XSS\')">Example</a>', $filter->filter( '<a href="http://example.com" onclick="alert(\'XSS\')">Example</a>' ) );
	}
}

?>

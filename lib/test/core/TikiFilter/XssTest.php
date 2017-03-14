<?php

/**
 * @group unit
 *
 */

class TikiFilter_XssTest extends TikiTestCase
{
	function testSimple()
	{
		$filter = new TikiFilter_PreventXss;

		$this->assertEquals(
			'<a href="http://example.com" on<x>click="alert(\'XSS\')">Example</a>',
			$filter->filter('<a href="http://example.com" onclick="alert(\'XSS\')">Example</a>')
		);
	}
}

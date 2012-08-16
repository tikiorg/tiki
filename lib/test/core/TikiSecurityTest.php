<?php

class TikiSecurityTest extends PHPUnit_Framework_TestCase
{
	function testValidData()
	{
		$data = array('foo' => 'bar');

		$security = new Tiki_Security('1234');
		$string = $security->encode($data);
		
		$this->assertEquals($data, $security->decode($string));
	}

	function testDecodeWithWrongHash()
	{
		$data = array('foo' => 'bar');

		$security = new Tiki_Security('1234');
		$string = $security->encode($data);
		
		$security = new Tiki_Security('4321');
		$this->assertNull($security->decode($string));
	}

	function testAlterData()
	{
		$data = array('foo' => 'bar');

		$security = new Tiki_Security('1234');
		$string = $security->encode($data);
		
		$string = str_replace('bar', 'baz', $string);
		$this->assertNull($security->decode($string));
	}
}


<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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


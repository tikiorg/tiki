<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiFilter_PrepareInputTest extends PHPUnit_Framework_TestCase
{
	function testNormalInput()
	{
		$input = array(
			'foo' => 'bar',
			'hello' => 'world',
		);

		$prepareInput = new TikiFilter_PrepareInput('.');

		$this->assertEquals($input, $prepareInput->prepare($input));
	}

	function testConvertArray()
	{
		$input = array(
			'foo.baz' => 'bar',
			'foo.bar' => 'baz',
			'hello' => 'world',
			'a.b.c' => '1',
			'a.b.d' => '2',
		);

		$expect = array(
			'foo' => array(
				'baz' => 'bar',
				'bar' => 'baz',
			),
			'hello' => 'world',
			'a' => array(
				'b' => array(
					'c' => '1',
					'd' => '2',
				),
			),
		);

		$prepareInput = new TikiFilter_PrepareInput('.');

		$this->assertEquals($expect, $prepareInput->prepare($input));
	}
}


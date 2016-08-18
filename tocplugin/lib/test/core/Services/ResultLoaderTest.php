<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ResultLoaderTest extends PHPUnit_Framework_TestCase
{
	private $read;

	function testFetchNothing()
	{
		$this->read = array(
			array(),
		);
		$this->assertLoaderData(array(), new Services_ResultLoader(array($this, 'read')));
	}

	function testFetchOnePartial()
	{
		$this->read = array(
			array(2, 4, 6),
		);
		$this->assertLoaderData(array(2, 4, 6), new Services_ResultLoader(array($this, 'read')));
	}

	function testFetchMultipleComplete()
	{
		$this->read = array(
			array(2, 4, 6),
			array(8, 9, 0),
			array(),
		);
		$this->assertLoaderData(array(2, 4, 6, 8, 9, 0), new Services_ResultLoader(array($this, 'read'), 3));
	}

	function testCompleteAndPartial()
	{
		$this->read = array(
			array(2, 4, 6),
			array(8),
		);
		$this->assertLoaderData(array(2, 4, 6, 8), new Services_ResultLoader(array($this, 'read'), 3));
	}

	function assertLoaderData($expect, $loader)
	{
		$accumulate = array();
		foreach ($loader as $value) {
			$accumulate[] = $value;
		}

		$this->assertEquals($expect, $accumulate);
	}

	function read($offset, $count)
	{
		$this->assertEquals(0, $offset % $count);
		return array_shift($this->read);
	}
}


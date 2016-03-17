<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$attributelib = TikiLib::lib('attribute');

class AttributeTest extends TikiTestCase
{
	function setUp()
	{
		parent::setUp();
		TikiDb::get()->query('DELETE FROM `tiki_object_attributes` WHERE `attribute` LIKE ?', array('tiki.test%'));
	}

	function tearDown()
	{
		parent::tearDown();
		TikiDb::get()->query('DELETE FROM `tiki_object_attributes` WHERE `attribute` LIKE ?', array('tiki.test%'));
	}

	function testNoAttributes()
	{
		$lib = new AttributeLib;

		$this->assertEquals(array(), $lib->get_attributes('test', 'HelloWorld'));
	}

	function testSetAttributes()
	{
		$lib = new AttributeLib;
		$lib->set_attribute('test', 'HelloWorld', 'tiki.test.abc', 121.22);
		$lib->set_attribute('test', 'HelloWorld', 'tiki.test.def', 111);
		$lib->set_attribute('test', 'Hello', 'tiki.test.ghi', 'no');
		$lib->set_attribute('test', 'HelloWorldAgain', 'tiki.test.jkl', 'no');

		$this->assertEquals(
			array('tiki.test.abc' => 121.22, 'tiki.test.def' => 111,),
			$lib->get_attributes('test', 'HelloWorld')
		);
	}

	function testReplaceValue()
	{
		$lib = new AttributeLib;
		$this->assertTrue($lib->set_attribute('test', 'HelloWorld', 'tiki.test.abc', 121.22));
		$this->assertTrue($lib->set_attribute('test', 'HelloWorld', 'tiki.test.abc', 'replaced'));

		$this->assertEquals(
			array('tiki.test.abc' => 'replaced',),
			$lib->get_attributes('test', 'HelloWorld')
		);
	}

	function testEnforceFormat()
	{
		$lib = new AttributeLib;
		$this->assertFalse($lib->set_attribute('test', 'HelloWorld', 'tiki.test', 121.22));

		$this->assertEquals(array(), $lib->get_attributes('test', 'HelloWorld'));
	}

	function testLowecase()
	{
		$lib = new AttributeLib;
		$this->assertTrue($lib->set_attribute('test', 'HelloWorld', 'tiki.TEST.aaa', 121.22));

		$this->assertEquals(
			array('tiki.test.aaa' => 121.22,),
			$lib->get_attributes('test', 'HelloWorld')
		);
	}

	function testFilterUndesired()
	{
		$lib = new AttributeLib;
		$this->assertTrue($lib->set_attribute('test', 'HelloWorld', 'tiki . test . aaa55bBb', 121.22));

		$this->assertEquals(
			array('tiki.test.aaa55bbb' => 121.22,),
			$lib->get_attributes('test', 'HelloWorld')
		);
	}

	function testRemoveEmpty()
	{
		$lib = new AttributeLib;
		$lib->set_attribute('test', 'HelloWorld', 'tiki.test.abc', 121.22);
		$lib->set_attribute('test', 'HelloWorld', 'tiki.test.abc', '');

		$this->assertEquals(array(), $lib->get_attributes('test', 'HelloWorld'));
	}
}


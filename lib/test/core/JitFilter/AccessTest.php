<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class JitFilter_AccessTest extends TikiTestCase
{
	private $array;

	function setUp()
	{
		$this->array = array(
			'foo' => 'bar',
			'bar' => 10,
			'baz' => array(
				'hello',
				'world',
			),
		);

		$this->array = new JitFilter($this->array);
	}

	function tearDown()
	{
		$this->array = null;
	}

	function testBasicAccess()
	{
		$this->assertEquals('bar', $this->array['foo']);
		$this->assertEquals(10, $this->array['bar']);
		$this->assertEquals('world', $this->array['baz'][1]);
	}

	function testRecursiveness()
	{
		$this->assertTrue($this->array['baz'] instanceof JitFilter);
	}

	function testDefinition()
	{
		$this->assertTrue(isset($this->array['baz']));
		$this->assertFalse(isset($this->array['hello']));
	}

	function testDirectArray()
	{
		$this->assertEquals(array(), array_diff(array('hello', 'world'), $this->array['baz']->asArray()));
	}

	function testKeys()
	{
		$this->assertEquals(array('foo', 'bar', 'baz'), $this->array->keys());
	}

	function testIsArray()
	{
		$this->assertTrue($this->array->isArray('baz'));
	}

	function testAsArray()
	{
		$this->assertEquals(array('bar'), $this->array->asArray('foo'));
		$this->assertEquals(array(), $this->array->asArray('not_exists'));
	}

	function testAsArraySplit()
	{
		$test = new JitFilter(array('foo' => '1|2a|3'));
		$test->setDefaultFilter(new Zend\Filter\Digits);

		$this->assertEquals(array('1', '2', '3'), $test->asArray('foo', '|'));
	}

	function testSubset()
	{
		$this->assertEquals(
			array(
				'foo' => $this->array['foo'],
				'baz' => $this->array->asArray('baz')
			),
			$this->array->subset(array('foo', 'baz'))->asArray()
		);
	}

	function testUnset()
	{
		unset($this->array['baz']);

		$this->assertFalse(isset($this->array['baz']));
	}

	function testSet()
	{
		$this->array['new'] = 'foo';

		$this->assertEquals($this->array['new'], 'foo');
	}

	function testGetSingleWithoutPresetGeneric()
	{
		$this->assertEquals($this->array->foo->filter(new Zend\Filter\StringToUpper), 'BAR');
	}

	function testGetSinfleWithoutPresetNamed()
	{
		$this->assertEquals($this->array->bar->digits(), '10');
	}

	function testGetStructuredWithoutPresetGeneric()
	{
		$filtered = $this->array->baz->filter(new Zend\Filter\StringToUpper);
		$this->assertEquals($filtered, array('HELLO', 'WORLD'));
	}

	function testGetStructuredWithoutPresetNamed()
	{
		$filtered = $this->array->baz->alpha();
		$this->assertEquals($filtered, array('hello', 'world'));
	}
}

<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ActionStepTest extends PHPUnit_Framework_TestCase
{
	function testMissingField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));

		$step = new Search_Action_ActionStep($action, []);
		$this->expectException(Search_Action_Exception::class);
		$this->expectExceptionMessage("Missing required action parameter or value: hello");
		$step->validate([]);
		$this->assertEquals(['hello'], $step->getFields());
	}

	function testMissingValueButNotRequired()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => false]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => null])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, []);
		$this->assertTrue($step->validate([]));
		$this->assertEquals(['hello'], $step->getFields());
	}

	function testValueProvidedStaticInDefinition()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => 'world'])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, ['hello' => 'world']);
		$this->assertTrue($step->validate([]));
		$this->assertEquals([], $step->getFields());
	}

	function testValueProvidedInEntryDirectly()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => 'world'])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, []);
		$this->assertTrue($step->validate(['hello' => 'world']));
		$this->assertEquals(['hello'], $step->getFields());
	}

	function testDefinitionDefersToSingleField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => 'world'])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, ['hello_field' => 'test']);
		$this->assertTrue($step->validate(['test' => 'world']));
		$this->assertEquals(['test'], $step->getFields());
	}

	function testDefinitionCoalesceField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => 'right'])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, ['hello_field_coalesce' => 'foo,bar,test,baz,hello']);
		$this->assertTrue($step->validate(['test' => 'right', 'baz' => 'wrong']));
		$this->assertEquals(['foo', 'bar', 'test', 'baz', 'hello'], $step->getFields());
	}

	function testDefinitionCoalesceFieldNoMatch()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello' => true]));

		$step = new Search_Action_ActionStep($action, ['hello_field_coalesce' => 'foo,bar,test,baz,hello']);
		$this->expectException(Search_Action_Exception::class);
		$step->validate([]);
		$this->assertEquals(['foo', 'bar', 'test', 'baz', 'hello'], $step->getFields());
	}

	function testRequiresValueAsArrayButMissing()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello+' => false]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => []])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, []);
		$this->assertTrue($step->validate([]));
		$this->assertEquals(['hello'], $step->getFields());
	}

	function testRequiresValueAsArrayAndSingleValue()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello+' => false]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => ['world']])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, ['hello' => 'world']);
		$this->assertTrue($step->validate([]));
		$this->assertEquals([], $step->getFields());
	}

	function testRequiresValueAsArrayAndMultipleValues()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(['hello+' => false]));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(['hello' => ['a', 'b']])))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, ['hello_field_multiple' => 'foo,bar,baz']);
		$this->assertTrue($step->validate(['foo' => 'a', 'baz' => 'b']));
		$this->assertEquals(['foo', 'bar', 'baz'], $step->getFields());
	}
}

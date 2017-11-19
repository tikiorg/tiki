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

class Transition_BasicTest extends PHPUnit_Framework_TestCase
{
	function testSimpleTransition()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A']);

		$this->assertTrue($transition->isReady());
	}

	function testAlreadyInTarget()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['B']);

		$this->assertFalse($transition->isReady());
	}

	function testInBoth()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A', 'B']);

		$this->assertFalse($transition->isReady());
	}

	function testExplainWhenReady()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A']);

		$this->assertEquals([], $transition->explain());
	}

	function testExplainWhenOriginNotMet()
	{
		$transition = new Tiki_Transition('A', 'B');

		$this->assertEquals(
			[['class' => 'missing', 'count' => 1, 'set' => ['A']],],
			$transition->explain()
		);
	}

	function testExplainWhenInTarget()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A', 'B']);

		$this->assertEquals(
			[['class' => 'extra', 'count' => 1, 'set' => ['B']],],
			$transition->explain()
		);
	}

	function testAddUnknownGuardType()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A']);
		$transition->addGuard('foobar', 5, ['D', 'E', 'F']);

		$this->assertEquals(
			[['class' => 'unknown', 'count' => 1, 'set' => ['foobar']],],
			$transition->explain()
		);
	}

	function testAddPassingCustomGuard()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A', 'C', 'F']);
		$transition->addGuard('exactly', 2, ['C', 'D', 'E', 'F']);

		$this->assertTrue($transition->isReady());
	}

	function testAddFailingCustomGuard()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A', 'C', 'F']);
		$transition->addGuard('exactly', 4, ['C', 'D', 'E', 'F', 'G']);

		$this->assertEquals(
			[['class' => 'missing', 'count' => 2, 'set' => ['D', 'E', 'G']],],
			$transition->explain()
		);
	}

	function testImpossibleCondition()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(['A', 'C', 'D', 'F']);
		$transition->addGuard('exactly', 4, ['C', 'D', 'E']);

		$this->assertEquals(
			[['class' => 'invalid', 'count' => 4, 'set' => ['C', 'D', 'E']],],
			$transition->explain()
		);
	}
}

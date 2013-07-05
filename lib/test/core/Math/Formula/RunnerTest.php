<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_RunnerTest extends TikiTestCase
{
	private $runner;

	function setUp()
	{
		$this->runner = new Math_Formula_Runner(
			array(
				'Math_Formula_Function_' => null,
				'Math_Formula_DummyFunction_' => null,
			)
		);
	}

	function testSimpleOperations()
	{
		$this->runner->setFormula('(mul (add foobar 2) test-variable)');
		$required = $this->runner->inspect();

		$this->assertEquals(array('foobar', 'test-variable'), $required);
	}

	function testSimpleOperationPreparsed()
	{
		$parser = new Math_Formula_Parser;
		$element = $parser->parse('(mul (add 1 2) test)');

		$this->runner->setFormula($element);
		$required = $this->runner->inspect();

		$this->assertEquals(array('test'), $required);
	}

	/**
	 * @expectedException Math_Formula_Runner_Exception
	 */
	function testUnknownOperator()
	{
		$this->runner->setFormula('(foobar abc)');
		$this->runner->inspect();
	}

	/**
	 * @expectedException Math_Formula_Runner_Exception
	 */
	function testNoFormulaSpecified()
	{
		$this->runner->inspect();
	}

	function testSimpleEvaluation()
	{
		$this->runner->setFormula('(add 1 2)');
		$this->assertEquals(3, $this->runner->evaluate());
	}

	function testMin()
	{
		$this->runner->setFormula('(min -10 0 20)');
		$this->assertEquals(-10, $this->runner->evaluate());

		$this->runner->setFormula('(min 10 20)');
		$this->assertEquals(10, $this->runner->evaluate());
	}

	function testMax()
	{
		$this->runner->setFormula('(max -10 0 20)');
		$this->assertEquals(20, $this->runner->evaluate());

		$this->runner->setFormula('(max -10 -5)');
		$this->assertEquals(-5, $this->runner->evaluate());
	}

	function testWithVariables()
	{
		$this->runner->setFormula('(mul foobar 2)');
		$this->runner->setVariables(array('foobar' => 2.5,));
		$this->assertEquals(5, $this->runner->evaluate());
	}

	/**
	 * @expectedException Math_Formula_Exception
	 */
	function testMissingVariable()
	{
		$this->runner->setFormula('(mul foobar 2)');
		$this->runner->evaluate();
	}

	function testSearchingForConfiguration()
	{
		$this->runner->setFormula('(testop (object test 123) (concat 456))');
		$this->runner->setVariables(array('test' => 'aaa'));

		$this->assertEquals(array('test'), $this->runner->inspect());

		$this->assertEquals('aaa123456', $this->runner->evaluate());
	}

	/**
	 * @expectedException Math_Formula_Exception
	 */
	function testInvalidData()
	{
		$this->runner->setFormula('(testop (object test) (concat 456))');

		$this->runner->inspect();
	}

	function testCamelCaseOperation()
	{
		$this->runner->setFormula('(forty-two)');
		$this->assertEquals(42, $this->runner->evaluate());
	}

	function testEmptyMap()
	{
		$this->runner->setFormula('(map)');

		$this->assertEquals(array(), $this->runner->evaluate());
	}

	function testGenerateMap()
	{
		$this->runner->setFormula('(map (a A) (b B))');
		$this->runner->setVariables(array('A' => 1, 'B' => 2));

		$this->assertEquals(array('a' => 1, 'b' => 2), $this->runner->evaluate());
	}

	function testEquals()
	{
		$this->runner->setFormula('(equals test 123)');

		$this->runner->setVariables(array('test' => 123));
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setVariables(array('test' => 456));
		$this->assertEquals(0, $this->runner->evaluate());

	}

	function testIf()
	{
		$this->runner->setFormula('(if condition then else)');
		$this->runner->setVariables(array(
			'condition' => 1,
			'then' => 123,
			'else' => 456,
		));

		$this->assertEquals(123, $this->runner->evaluate());

		$this->runner->setVariables(array(
			'condition' => 0,
			'then' => 123,
			'else' => 456,
		));

		$this->assertEquals(456, $this->runner->evaluate());
	}

	function testIfWithoutElse()
	{
		$this->runner->setFormula('(if condition then)');
		$this->runner->setVariables(array(
			'condition' => 1,
			'then' => 123,
		));

		$this->assertEquals(123, $this->runner->evaluate());

		$this->runner->setVariables(array(
			'condition' => 0,
			'then' => 123,
		));

		$this->assertEquals(0, $this->runner->evaluate());
	}

	function testAnd()
	{
		$this->runner->setFormula('(and)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(and 0)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(and 1 1 0 1 1)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(and 1 1 1 1 0)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(and 1)');
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setFormula('(and 1 1 1 2 1)');
		$this->assertEquals(1, $this->runner->evaluate());
	}

	function testOr()
	{
		$this->runner->setFormula('(or)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(or 0)');
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setFormula('(or 1 1 0 1 1)');
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setFormula('(or 1 1 1 1 0)');
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setFormula('(or 1)');
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setFormula('(or 0 0 0 0 0)');
		$this->assertEquals(0, $this->runner->evaluate());
	}
}


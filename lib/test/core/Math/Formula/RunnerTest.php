<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
			[
				'Math_Formula_Function_' => null,
				'Math_Formula_DummyFunction_' => null,
			]
		);
	}

	function testSimpleOperations()
	{
		$this->runner->setFormula('(mul (add foobar 2) test-variable)');
		$required = $this->runner->inspect();

		$this->assertEquals(['foobar', 'test-variable'], $required);
	}

	function testSimpleOperationPreparsed()
	{
		$parser = new Math_Formula_Parser;
		$element = $parser->parse('(mul (add 1 2) test)');

		$this->runner->setFormula($element);
		$required = $this->runner->inspect();

		$this->assertEquals(['test'], $required);
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

	function testSum()
	{
		$this->runner->setFormula('(add list)');
		$this->runner->setVariables(['list' => [1,2,3]]);
		$this->assertEquals(6, $this->runner->evaluate());
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
		$this->runner->setVariables(['foobar' => 2.5,]);
		$this->assertEquals(5, $this->runner->evaluate());
	}

	function testProductList()
	{
		$this->runner->setFormula('(mul list)');
		$this->runner->setVariables(['list' => [2.5,2,4]]);
		$this->assertEquals(20, $this->runner->evaluate());
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
		$this->runner->setVariables(['test' => 'aaa']);

		$this->assertEquals(['test'], $this->runner->inspect());

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

		$this->assertEquals([], $this->runner->evaluate());
	}

	function testGenerateMap()
	{
		$this->runner->setFormula('(map (a A) (b B))');
		$this->runner->setVariables(['A' => 1, 'B' => 2]);

		$this->assertEquals(['a' => 1, 'b' => 2], $this->runner->evaluate());
	}

	function testEquals()
	{
		$this->runner->setFormula('(equals test 123)');

		$this->runner->setVariables(['test' => 123]);
		$this->assertEquals(1, $this->runner->evaluate());

		$this->runner->setVariables(['test' => 456]);
		$this->assertEquals(0, $this->runner->evaluate());
	}

	function testNotEquals()
	{
		$this->runner->setFormula('(not-equals test 123)');

		$this->runner->setVariables(['test' => 123]);
		$this->assertEquals(0, $this->runner->evaluate());

		$this->runner->setVariables(['test' => 456]);
		$this->assertEquals(1, $this->runner->evaluate());
	}

	function testIf()
	{
		$this->runner->setFormula('(if condition then else)');
		$this->runner->setVariables(
			[
				'condition' => 1,
				'then' => 123,
				'else' => 456,
			]
		);

		$this->assertEquals(123, $this->runner->evaluate());

		$this->runner->setVariables(
			[
				'condition' => 0,
				'then' => 123,
				'else' => 456,
			]
		);

		$this->assertEquals(456, $this->runner->evaluate());
	}

	function testIfWithoutElse()
	{
		$this->runner->setFormula('(if condition then)');
		$this->runner->setVariables(
			[
				'condition' => 1,
				'then' => 123,
			]
		);

		$this->assertEquals(123, $this->runner->evaluate());

		$this->runner->setVariables(
			[
				'condition' => 0,
				'then' => 123,
			]
		);

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

	function testExtractParts()
	{
		$this->runner->setFormula('(split-list (content string) (separator :) (keys object-type object-id))');
		$this->runner->setVariables(
			[
				'string' => "wiki page:HomePage\ntrackeritem:2\ntrackeritem:3",
			]
		);

		$this->assertEquals([
			['object-type' => 'wiki page', 'object-id' => 'HomePage'],
			['object-type' => 'trackeritem', 'object-id' => '2'],
			['object-type' => 'trackeritem', 'object-id' => '3'],
		], $this->runner->evaluate());
	}

	function testSplitWithSingleKey()
	{
		$this->runner->setFormula('(split-list (content string) (separator ,) (key id))');
		$this->runner->setVariables(
			[
				'string' => "214,266,711",
			]
		);

		$this->assertEquals([
			['id' => '214'],
			['id' => '266'],
			['id' => '711'],
		], $this->runner->evaluate());
	}

	function testMapList()
	{
		$this->runner->setFormula('(for-each (list list) (formula (mul a b c)))');
		$this->runner->setVariables([
			'c' => 10,
			'list' => [
				['a' => 1, 'b' => 2],
				['a' => 2, 'b' => 3],
				['a' => 3, 'b' => 4],
			],
		]);

		$this->assertEquals([20, 60, 120], $this->runner->evaluate());
	}

	function testAverageList()
	{
		$this->runner->setFormula('(avg a b)');
		$this->runner->setVariables([
			'a' => 1,
			'b' => [3, 5, 7],
		]);

		$this->assertEquals(4, $this->runner->evaluate());
	}

	/**
	 * @dataProvider stringConcats
	 */
	function testStringConcat($in, $out)
	{
		$this->runner->setFormula($in);
		$this->runner->setVariables([
			'a' => 'hello',
			'b' => 'world',
		]);

		$this->assertEquals($out, $this->runner->evaluate());
	}

	function stringConcats()
	{
		return [
			['(str a b)', 'a b'],
			['(str (mul 3 2)b)', '6 b'],
			['(str Say: (eval a b) !)', 'Say: hello world !'],
			['(concat "Say: " a " " b " !")', 'Say: hello world !'],
			['(str "Say:" a b "!")', 'Say: a b !'],
		];
	}
}

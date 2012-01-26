<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_RunnerTest extends TikiTestCase
{
	private $runner;

	function setUp() {
		$this->runner = new Math_Formula_Runner( array(
			'Math_Formula_Function_' => realpath( dirname(__FILE__) . '/../../../../core/Math/Formula/Function' ),
			'Math_Formula_DummyFunction_' => realpath( dirname(__FILE__) . '/DummyFunction' ),
		) );
	}

	function testSimpleOperations() {
		$this->runner->setFormula( '(mul (add foobar 2) test-variable)' );
		$required = $this->runner->inspect();

		$this->assertEquals( array( 'foobar', 'test-variable' ), $required );
	}

	function testSimpleOperationPreparsed() {
		$parser = new Math_Formula_Parser;
		$element = $parser->parse( '(mul (add 1 2) test)' );
		
		$this->runner->setFormula( $element );
		$required = $this->runner->inspect();

		$this->assertEquals( array( 'test' ), $required );
	}

	/**
	 * @expectedException Math_Formula_Runner_Exception
	 */
	function testUnknownOperator() {
		$this->runner->setFormula( '(foobar abc)' );
		$this->runner->inspect();
	}

	/**
	 * @expectedException Math_Formula_Runner_Exception
	 */
	function testNoFormulaSpecified() {
		$this->runner->inspect();
	}

	function testSimpleEvaluation() {
		$this->runner->setFormula( '(add 1 2)' );
		$this->assertEquals( 3, $this->runner->evaluate() );
	}

	function testWithVariables() {
		$this->runner->setFormula( '(mul foobar 2)' );
		$this->runner->setVariables( array(
			'foobar' => 2.5,
		) );
		$this->assertEquals( 5, $this->runner->evaluate() );
	}

	/**
	 * @expectedException Math_Formula_Exception
	 */
	function testMissingVariable() {
		$this->runner->setFormula( '(mul foobar 2)' );
		$this->runner->evaluate();
	}

	function testSearchingForConfiguration() {
		$this->runner->setFormula( '(testop (object test 123) (concat 456))' );
		$this->runner->setVariables( array( 'test' => 'aaa' ) );
		
		$this->assertEquals( array( 'test' ), $this->runner->inspect() );

		$this->assertEquals( 'aaa123456', $this->runner->evaluate() );
	}

	/**
	 * @expectedException Math_Formula_Exception
	 */
	function testInvalidData() {
		$this->runner->setFormula( '(testop (object test) (concat 456))' );
		
		$this->runner->inspect();
	}

	function testCamelCaseOperation() {
		$this->runner->setFormula( '(forty-two)' );
		$this->assertEquals( 42, $this->runner->evaluate() );
	}
}


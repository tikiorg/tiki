<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_ParserTest extends TikiTestCase
{
	function testEmptyElement() {
		$parser = new Math_Formula_Parser;

		$element = new Math_Formula_Element( 'score' );

		$this->assertEquals( $element, $parser->parse( '(score)' ) );
	}

	function testWithArguments() {
		$parser = new Math_Formula_Parser;

		$element = new Math_Formula_Element( 'object', array( 'input-type', 'input-object-id' ) );
		$this->assertEquals( $element, $parser->parse( '(object input-type input-object-id)' ) );
	}

	function testNesting() {
		$parser = new Math_Formula_Parser;

		$element = new Math_Formula_Element( 'score', array( 
			new Math_Formula_Element( 'object', array( 'type', 'object' ) ) ) );

		$this->assertEquals( $element, $parser->parse( '(score (object type object))' ) );
	}

	function testMultipleElements() {
		$parser = new Math_Formula_Parser;

		$element = new Math_Formula_Element( 'score', array( 
			new Math_Formula_Element( 'object', array( 'type', 'object' ) ),
			new Math_Formula_Element( 'range', array( 3600 ) ),
		) );

		$this->assertEquals( $element, $parser->parse( '(score (object type object) (range 3600))' ) );
	}

	function testMultipleNesting() {
		$parser = new Math_Formula_Parser;

		$element = new Math_Formula_Element( 'score', array( 
			new Math_Formula_Element( 'object', array( 'type', 'object' ) ),
			new Math_Formula_Element( 'range', array(
				new Math_Formula_Element( 'mul', array( 3600, 60 ) ),
			) ),
		) );

		$this->assertEquals( $element, $parser->parse( '(score (object type object) (range (mul 3600 60)))' ) );
	}

	function testSkipComments() {
		$equivalent = '(score (object type object) (range (mul 3600 60)))';
		$documented = <<<DOC
(score
	(comment calculate for a 60 hour range)
	(object type object)
	(range (mul
		(comment 3600 is an hour, times 60)
		3600 60))
)
DOC;

		$parser = new Math_Formula_Parser;
		$this->assertEquals( $parser->parse( $equivalent ), $parser->parse( $documented ) );
	}

	function testWithZero() {
		$parser = new Math_Formula_Parser;
		$element = new Math_Formula_Element( 'add', array( 
			new Math_Formula_Element( 'default', array( 0 ) ),
			new Math_Formula_Element( 'attribute' ),
		) );

		$this->assertEquals( $element, $parser->parse( '
		(add
			(default 0)
			(attribute)
		)' ) );
	}

	function badStrings() {
		return array(
			'noOpening' => array( 'test' ),
			'noToken' => array( '()' ),
			'doubles' => array( '((test))' ),
			'trail' => array( '(test) foo' ),
			'unfinished' => array( '(score (object type object) (range 3600)' ),
		);
	}

	/**
	 * @dataProvider badStrings
	 * @expectedException Math_Formula_Parser_Exception
	 */
	function testBadParse( $string ) {
		$parser = new Math_Formula_Parser;

		$parser->parse( $string );
	}
}


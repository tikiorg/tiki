<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_ElementTest extends TikiTestCase
{
	function testGetArgument() {
		$element = new Math_Formula_Element( 'test', array( 1, 2, 3 ) );
		$this->assertEquals( 1, $element[0] );
		$this->assertEquals( 3, $element[2] );
		$this->assertNull( $element[3] );
	}

	function testGetKey() {
		$element = new Math_Formula_Element( 'test', array( 
			1,
			$target = new Math_Formula_Element( 'object', array( 'type', 'id' ) ),
			2,
			new Math_Formula_Element( 'object', array( 'foobar', 'baz' ) ),
			3,
		) );

		$this->assertSame( $target, $element->object );
		$this->assertNull( $element->foobar );
	}
}


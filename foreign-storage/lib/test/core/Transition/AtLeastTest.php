<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** 
 * @group unit
 * 
 */

class Transition_AtLeastTest extends PHPUnit_Framework_TestCase
{
	function testOver() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atLeast', 2, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
		), $transition->explain() );
	}

	function testRightOn() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atLeast', 3, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
		), $transition->explain() );
	}

	function testUnder() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atLeast', 4, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
			array( 'class' => 'missing', 'count' => 1, 'set' => array( 'E', 'G' ) ),
		), $transition->explain() );
	}

	function testImpossibleCondition() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atLeast', 4, array( 'C', 'D', 'E' ) );

		$this->assertEquals( array(
			array( 'class' => 'invalid', 'count' => 4, 'set' => array( 'C', 'D', 'E' ) ),
		), $transition->explain() );
	}
}

<?php

/** 
 * @group unit
 * 
 */

class Transition_AtMostTest extends PHPUnit_Framework_TestCase
{
	function testOver() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atMost', 2, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
			array( 'class' => 'extra', 'count' => 1, 'set' => array( 'C', 'D', 'F' ) ),
		), $transition->explain() );
	}

	function testRightOn() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atMost', 3, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
		), $transition->explain() );
	}

	function testUnder() {
		$transition = new Transition( 'A', 'B' );
		$transition->setStates( array( 'A', 'C', 'D', 'F' ) );
		$transition->addGuard( 'atMost', 4, array( 'C', 'D', 'E', 'F', 'G' ) );

		$this->assertEquals( array(
		), $transition->explain() );
	}
}

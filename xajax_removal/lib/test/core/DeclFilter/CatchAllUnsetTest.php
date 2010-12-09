<?php

/**
 * @group unit
 * 
 */

class DeclFilter_CatchAllUnsetTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_CatchAllUnsetRule();

		$this->assertTrue( $rule->match( 'hello' ) );
	}

	function testApply()
	{
		$rule = new DeclFilter_CatchAllUnsetRule();

		$data = array(
			'hello' => '123abc',
		);

		$rule->apply( $data, 'hello' );

		$this->assertFalse( isset( $data['hello'] ) ); 
	}
}

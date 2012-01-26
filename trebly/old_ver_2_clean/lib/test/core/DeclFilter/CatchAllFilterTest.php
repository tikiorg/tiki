<?php

/** 
 * @group unit
 * 
 */

class DeclFilter_CatchAllFilterTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_CatchAllFilterRule( 'digits' );

		$this->assertTrue( $rule->match( 'hello' ) );
	}

	function testApply()
	{
		$rule = new DeclFilter_CatchAllFilterRule( 'digits' );

		$data = array(
			'hello' => '123abc',
		);

		$rule->apply( $data, 'hello' );

		$this->assertEquals( $data['hello'], '123' );
	}

	function testApplyRecursive()
	{
		$rule = new DeclFilter_CatchAllFilterRule( 'digits' );
		$rule->applyOnElements();

		$data = array(
			'hello' => array(
				'abc123',
				'abc456',
			),
		);

		$rule->apply( $data, 'hello' );

		$this->assertEquals( $data['hello'][0], '123' );
		$this->assertEquals( $data['hello'][1], '456' );
	}
}

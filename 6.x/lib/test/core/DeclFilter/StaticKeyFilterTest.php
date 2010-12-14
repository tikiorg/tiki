<?php

/**
 * @group unit
 * 
 */

class DeclFilter_StaticKeyFilterTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_StaticKeyFilterRule( array(
			'hello' => 'digits',
			'world' => 'alpha',
		) );

		$this->assertTrue( $rule->match( 'hello' ) );
		$this->assertTrue( $rule->match( 'world' ) );
		$this->assertFalse( $rule->match( 'baz' ) );
	}

	function testApply()
	{
		$rule = new DeclFilter_StaticKeyFilterRule( array(
			'hello' => 'digits',
			'world' => 'alpha',
		) );

		$data = array(
			'hello' => '123abc',
			'world' => '123abc',
			'foo' => '123abc',
		);

		$rule->apply( $data, 'hello' );
		$rule->apply( $data, 'world' );

		$this->assertEquals( $data['hello'], '123' );
		$this->assertEquals( $data['world'], 'abc' );
		$this->assertEquals( $data['foo'], '123abc' );
	}
}

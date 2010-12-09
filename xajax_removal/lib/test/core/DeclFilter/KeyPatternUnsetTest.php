<?php

/**
 * @group unit
 * 
 */

class DeclFilter_KeyPatternUnsetTest extends TikiTestCase
{
	function testMatch()
	{
		$rule = new DeclFilter_KeyPatternUnsetRule( array(
			'/^foo_\d+$/',
			'/^bar_[a-z]+$/',
		) );

		$this->assertTrue( $rule->match( 'foo_123' ) );
		$this->assertTrue( $rule->match( 'bar_abc' ) );
		$this->assertFalse( $rule->match( 'foo_abc' ) );
		$this->assertFalse( $rule->match( 'baz' ) );
	}

	function testApply()
	{
		$rule = new DeclFilter_KeyPatternUnsetRule( array(
			'/^foo_\d+$/',
			'/^bar_[a-z]+$/',
		) );

		$data = array(
			'foo_123' => '123abc',
			'bar_abc' => '123abc',
			'foo' => '123abc',
		);

		$rule->apply( $data, 'foo_123' );
		$rule->apply( $data, 'bar_abc' );

		$this->assertFalse( isset( $data['foo_123'] ) );
		$this->assertFalse( isset( $data['bar_abc'] ) );
		$this->assertEquals( $data['foo'], '123abc' );
	}
}

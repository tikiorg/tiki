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

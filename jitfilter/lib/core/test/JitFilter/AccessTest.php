<?php

class JitFilter_AccessTest extends PHPUnit_Framework_TestCase
{
	private $array;

	function setUp()
	{
		$this->array = array(
			'foo' => 'bar',
			'bar' => 10,
			'baz' => array(
				'hello',
				'world',
			),
		);

		$this->array = new JitFilter( $this->array );
	}

	function tearDown()
	{
		$this->array = null;
	}

	function testBasicAccess()
	{
		$this->assertEquals( 'bar', $this->array['foo'] );
		$this->assertEquals( 10, $this->array['bar'] );
		$this->assertEquals( 'world', $this->array['baz'][1] );
	}

	function testRecursiveness()
	{
		$this->assertTrue( $this->array['baz'] instanceof JitFilter );
	}

	function testDefinition()
	{
		$this->assertTrue( isset( $this->array['baz'] ) );
		$this->assertFalse( isset( $this->array['hello'] ) );
	}
}

?>

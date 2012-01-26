<?php

/**
 * @group unit
 * 
 */

class JitFilter_IteratorTest extends TikiTestCase
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
		$this->array->setDefaultFilter( new Zend_Filter_StringToUpper );
	}

	function tearDown()
	{
		$this->array = null;
	}

	function testForeach()
	{
		foreach( $this->array as $key => $value ) {
			switch( $key ) {
			case 'foo':
				$this->assertEquals( 'BAR', $value );
				break;
			case 'bar':
				$this->assertEquals( 10, $value );
				break;
			case 'baz':
				$this->assertEquals( 2, count( $value ) );
				break;
			default:
				$this->assertTrue( false, 'Unknown key found' );
			}
		}
	}
}

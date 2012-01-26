<?php

/**
 * @group unit
 * 
 */
class JitFilter_FilterTest extends TikiTestCase
{
	private $array;

	function setUp()
	{
		$this->array = array(
			'foo' => 'bar123',
			'bar' => 10,
			'baz' => array(
				'hello',
				'world !',
			),
			'content' => '10 > 5 <script>',
		);

		$this->array = new JitFilter( $this->array );
		$this->array->setDefaultFilter( new Zend_Filter_Alnum );
	}

	function tearDown()
	{
		$this->array = null;
	}

	function testValid()
	{
		$this->assertEquals( 'bar123', $this->array['foo'] );
		$this->assertEquals( 10, $this->array['bar'] );
	}

	function testInvalid()
	{
		$this->assertEquals( 'world', $this->array['baz'][1] );
	}

	function testSpecifiedFilter()
	{
		$this->assertEquals( 'bar123', $this->array['foo'] );

		$this->array->replaceFilter( 'foo', new Zend_Filter_Digits );
		$this->assertEquals( '123', $this->array['foo'] );
	}

	function testMultipleFilters()
	{
		$this->array->replaceFilters( array(
			'foo' => new Zend_Filter_Digits,
			'content' => new Zend_Filter_StripTags,
			'baz' => array(
				1 => new Zend_Filter_StringToUpper,
			),
		) );

		$this->assertEquals( '123', $this->array['foo'] );
		$this->assertEquals( '10  5 ', $this->array['content'] );
		$this->assertEquals( 'WORLD !', $this->array['baz'][1] );
	}

	function testNestedDefault()
	{
		$this->array->replaceFilters( array(
			'foo' => new Zend_Filter_Digits,
			'content' => new Zend_Filter_StripTags,
			'baz' => new Zend_Filter_StringToUpper,
		) );

		$this->assertEquals( '123', $this->array['foo'] );
		$this->assertEquals( '10  5 ', $this->array['content'] );
		$this->assertEquals( 'WORLD !', $this->array['baz'][1] );

		$this->array->replaceFilter( 'baz', new Zend_Filter_Alpha );
		$this->assertEquals( 'world', $this->array['baz'][1] );

		$this->array->replaceFilters( array(
			'baz' => array(
				1 => new Zend_Filter_Digits,
			),
		) );

		$this->assertEquals( 'hello', $this->array['baz'][0] );
		$this->assertEquals( '', $this->array['baz'][1] );
	}
}

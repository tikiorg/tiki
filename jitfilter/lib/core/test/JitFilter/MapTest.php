<?php

class JitFilter_MapTest extends PHPUnit_Framework_TestCase
{
	private $array;

	function testDirect()
	{
		$this->assertTrue( JitFilter::mapFilter( 'digits' ) instanceof Zend_Filter_Digits );
		$this->assertTrue( JitFilter::mapFilter( 'alpha' ) instanceof Zend_Filter_Alpha );
		$this->assertTrue( JitFilter::mapFilter( 'alnum' ) instanceof Zend_Filter_Alnum );
		$this->assertTrue( JitFilter::mapFilter( 'striptags' ) instanceof Zend_Filter_StripTags );
		$this->assertTrue( JitFilter::mapFilter( 'xss' ) instanceof JitFilter_PreventXss );
		$this->assertTrue( JitFilter::mapFilter( 'word' ) instanceof JitFilter_Word );
	}

	function testComposed()
	{
		$filter = new JitFilter( array( 'foo' => 'test123' ) );
		$filter->replaceFilter( 'foo', 'digits' );

		$this->assertEquals( '123', $filter['foo'] );
	}

	function testDefault()
	{
		$filter = new JitFilter( array( 'foo' => 'test123' ) );
		$filter->setDefaultFilter( 'digits' );

		$this->assertEquals( '123', $filter['foo'] );
	}
}

?>

<?php

class Search_Formatter_ArrayBuilderTest extends PHPUnit_Framework_TestCase
{
	private $builder;

	function setUp()
	{
		$this->builder = new Search_Formatter_ArrayBuilder;
	}

	function testEmpty()
	{
		$this->assertEquals(array(), $this->builder->getData(''));
	}

	function testSingleValue()
	{
		$string = <<<STR
{hello foo=bar}
STR;
		
		$this->assertEquals(array('hello' => array('foo' => 'bar')), $this->builder->getData($string));
	}

	function testDifferentKeys()
	{
		$string = <<<STR
{hello foo=bar bar=test}
{test foo=bar}
STR;
		
		$this->assertEquals(array(
			'hello' => array('foo' => 'bar', 'bar' => 'test'),
			'test' => array('foo' => 'bar'),
		), $this->builder->getData($string));
	}

	function testGenerateList()
	{
		$string = <<<STR
{test foo=bar}
{test bar=baz}
STR;
		
		$this->assertEquals(array(
			'test' => array(
				array('foo' => 'bar'),
				array('bar' => 'baz'),
			),
		), $this->builder->getData($string));
	}
}


<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

		$this->assertEquals(
			array(
				'hello' => array('foo' => 'bar', 'bar' => 'test'),
				'test' => array('foo' => 'bar'),
			),
			$this->builder->getData($string)
		);
	}

	function testGenerateList()
	{
		$string = <<<STR
{test foo=bar}
{test bar=baz}
STR;

		$this->assertEquals(
			array(
				'test' => array(
					array('foo' => 'bar'),
					array('bar' => 'baz'),
				),
			),
			$this->builder->getData($string)
		);
	}
}


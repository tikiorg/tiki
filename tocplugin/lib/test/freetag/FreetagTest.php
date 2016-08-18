<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$freetaglib = TikiLib::lib('freetag');

class FreetagTest extends TikiTestCase
{

	private $lib;

	function setUp()
	{
		$this->lib = new FreetagLib();
	}

	function testDumbParseTagsShouldReturnEmptyArray()
	{
		$this->assertEquals(array(), $this->lib->dumb_parse_tags(null));
		$this->assertEquals(array(), $this->lib->dumb_parse_tags(array()));
		$this->assertEquals(array(), $this->lib->dumb_parse_tags(''));
	}

	function testDumbParseTagsShouldReturnParsedArray()
	{
		//TODO: mock FreetagLib::_parse_tag() and FreetagLib::normalize_tag()
		$expectedResult = array(
				'data' => array(
					0 => array('tag' => 'first'),
					1 => array('tag' => 'multiple word tag'),
					2 => array('tag' => 'third'),
					3 => array('tag' => 'another multiple word tag')
					),
				'cant' => 4,
				);

		$tagString = 'first "multiple word tag" third "another Multiple Word tag"';

		$this->assertEquals($expectedResult, $this->lib->dumb_parse_tags($tagString));
	}

}


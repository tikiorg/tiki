<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiLib_MultiExplodeTest extends PHPUnit_Framework_TestCase
{
	private $saved;

	function setUp()
	{
		global $prefs;
		$this->saved = $prefs['namespace_separator'];
	}

	function tearDown()
	{
		global $prefs;
		$prefs['namespace_separator'] = $this->saved;
	}

	function testSimple()
	{
		$lib = TikiLib::lib('tiki');
		$this->assertEquals(array('A', 'B'), $lib->multi_explode(':', 'A:B'));
		$this->assertEquals(array('A::B'), $lib->multi_explode(':', 'A::B'));
		$this->assertEquals(array('A:::B'), $lib->multi_explode(':', 'A:::B'));
	}

	function testIgnoreCharactersUsedInNamespace()
	{
		global $prefs;
		$lib = TikiLib::lib('tiki');

		$prefs['namespace_separator'] = ':+:';
		$this->assertEquals(array('A:+:B:+:C', 'A:+:B'), $lib->multi_explode(':', 'A:+:B:+:C:A:+:B'));
		$this->assertEquals(array('A', '-', 'B:+:C', 'A:+:B'), $lib->multi_explode(':', 'A:-:B:+:C:A:+:B'));

		$prefs['namespace_separator'] = ':-:';
		$this->assertEquals(array('A', '+', 'B', '+', 'C', 'A', '+', 'B'), $lib->multi_explode(':', 'A:+:B:+:C:A:+:B'));
		$this->assertEquals(array('A:-:B', '+', 'C', 'A', '+', 'B'), $lib->multi_explode(':', 'A:-:B:+:C:A:+:B'));
	}

	function testSimpleImplode()
	{
		$lib = TikiLib::lib('tiki');
		$this->assertEquals('A:B', $lib->multi_implode(':', array('A', 'B')));
		$this->assertEquals('A+C:B+D', $lib->multi_implode(array(':', '+'), array(array('A', 'C'), array('B', 'D'))));
	}
}


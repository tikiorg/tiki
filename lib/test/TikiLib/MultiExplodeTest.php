<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiLib_MultiExplodeTest extends PHPUnit_Framework_TestCase
{
	function testSimple()
	{
		$lib = TikiLib::lib('tiki');
		$this->assertEquals(array('A', 'B'), $lib->multi_explode(':', 'A:B'));
		$this->assertEquals(array('A::B'), $lib->multi_explode(':', 'A::B'));
		$this->assertEquals(array('A:::B'), $lib->multi_explode(':', 'A:::B'));
	}
}


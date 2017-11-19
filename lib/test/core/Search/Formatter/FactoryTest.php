<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_FactoryTest extends PHPUnit_Framework_TestCase
{
	private $plugin;

	function setUp()
	{
		$this->plugin = new Search_Formatter_Plugin_WikiTemplate("");
	}

	function testInstantiation()
	{
		$formatter = Search_Formatter_Factory::newFormatter($this->plugin);
		$this->assertEquals('Search_Formatter', get_class($formatter));
	}

	function testSequence()
	{
		$formatter1 = Search_Formatter_Factory::newFormatter($this->plugin);
		$formatter2 = Search_Formatter_Factory::newFormatter($this->plugin);
		$this->assertEquals($formatter1->getCounter() + 1, $formatter2->getCounter());
	}
}

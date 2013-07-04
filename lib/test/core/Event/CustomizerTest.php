<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_CustomizerTest extends PHPUnit_Framework_TestCase
{
	private $manager;
	private $called;

	function setUp()
	{
		$this->called = 0;
		$this->manager = new Tiki_Event_Manager;
	}

	function testBindThroughCustomizedEventFilter()
	{
		$this->manager->bind('custom.event', array($this, 'callbackAdd'));

		$customizer = new Tiki_Event_Customizer;
		$customizer->addRule('tiki.trackeritem.save', '(event-trigger custom.event)');
		$customizer->bind($this->manager);

		$this->manager->trigger('tiki.trackeritem.save');

		$this->assertEquals(1, $this->called);
	}

	function testPassCustomArguments()
	{
		$this->manager->bind('custom.event', array($this, 'callbackAdd'));

		$customizer = new Tiki_Event_Customizer;
		$customizer->addRule('tiki.trackeritem.save', '(event-trigger custom.event 
			(arguments
				(amount (add 2 3))
				(test 4)
			))');
		$customizer->bind($this->manager);

		$this->manager->trigger('tiki.trackeritem.save');

		$this->assertEquals(5, $this->called);
	}

	function callbackAdd($arguments)
	{
		$this->called += isset($arguments['amount']) ? $arguments['amount'] : 1;
	}

	function callbackMultiply($arguments)
	{
		$this->called *= isset($arguments['amount']) ? $arguments['amount'] : 2;
	}
}


<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Event_ManagerTest extends PHPUnit_Framework_TestCase
{
	private $called;

	function setUp()
	{
		$this->called = 0;
	}

	function testTriggerUnknown()
	{
		$manager = new Event_Manager;
		$manager->trigger('tiki.wiki.update');

		$this->assertEquals(0, $this->called);
	}

	function testBindAndTrigger()
	{
		$manager = new Event_Manager;
		$manager->bind('tiki.wiki.update', array($this, 'callbackAdd'));

		$manager->trigger('tiki.wiki.update');

		$this->assertEquals(1, $this->called);
	}

	function testChaining()
	{
		$manager = new Event_Manager;

		$manager->bind('tiki.wiki.update', 'tiki.wiki.save');
		$manager->bind('tiki.wiki.save', 'tiki.save');

		$manager->bind('tiki.save', array($this, 'callbackAdd'));
		$manager->bind('tiki.wiki.save', array($this, 'callbackMultiply'));
		$manager->bind('tiki.wiki.update', array($this, 'callbackMultiply'));

		$manager->trigger('tiki.wiki.update');

		$this->assertEquals(4, $this->called);
	}

	function testProvideBindingArguments()
	{
		$manager = new Event_Manager;
		$manager->bind('tiki.wiki.update', array($this, 'callbackAdd'), array(
			'amount' => 4,
		));
		$manager->bind('tiki.wiki.update', array($this, 'callbackAdd'), array(
			'amount' => 5,
		));

		$manager->trigger('tiki.wiki.update');

		$this->assertEquals(9, $this->called);
	}

	function testCalltimeArgumentsOverrideBinding()
	{
		$manager = new Event_Manager;

		$manager->bind('tiki.wiki.update', 'tiki.wiki.save');

		$manager->bind('tiki.save', array($this, 'callbackAdd'));
		$manager->bind('tiki.wiki.save', array($this, 'callbackAdd'), array('amount' => 3));
		$manager->bind('tiki.wiki.update', array($this, 'callbackMultiply'));

		$manager->trigger('tiki.wiki.update', array('amount' => 4));

		$this->assertEquals(16, $this->called);
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


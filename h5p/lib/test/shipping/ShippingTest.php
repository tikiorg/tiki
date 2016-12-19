<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once 'lib/shipping/shippinglib.php';

class ShippingTest extends TikiTestCase implements ShippingProvider
{
	private $from;
	private $to;
	private $packages;

	function setUp()
	{
		$this->from = null;
		$this->to = null;
		$this->packages = null;
	}

	function testWithoutProvider()
	{
		$lib = new ShippingLib;

		$this->assertEquals(array(), $lib->getRates(array('zip' => '12345'), array('zip' => '23456'), array(array('weight' => 5))));
	}

	function testCountryPreserved()
	{
		$lib = new ShippingLib;
		$lib->addProvider($this);

		$lib->getRates(array('zip' => '12345', 'country' => 'FR'), array('zip' => '23456'), array(array('weight' => 5)));

		$this->assertEquals('FR', $this->from['country']);
	}

	function testCountryCompleted()
	{
		$lib = new ShippingLib;
		$lib->addProvider($this);

		$lib->getRates(array('zip' => '12345'), array('zip' => 'A1B 2C3'), array(array('weight' => 5)));

		$this->assertEquals('US', $this->from['country']);
		$this->assertEquals('CA', $this->to['country']);
	}

	function testZipUpperCased()
	{
		$lib = new ShippingLib;
		$lib->addProvider($this);

		$lib->getRates(array('zip' => '12345'), array('zip' => 'a1b 2c3'), array(array('weight' => 5)));

		$this->assertEquals('A1B 2C3', $this->to['zip']);
		$this->assertEquals('CA', $this->to['country']);
	}

	function testUnknownFormat()
	{
		$lib = new ShippingLib;
		$lib->addProvider($this);

		$lib->getRates(array('zip' => '12345678900X'), array('zip' => 'A1B 2C3'), array(array('weight' => 5)));

		$this->assertArrayNotHasKey('country', $this->from);
	}

	function testPackageExpansion()
	{
		$lib = new ShippingLib;
		$lib->addProvider($this);

		$lib->getRates(array('zip' => '12345678900X'), array('zip' => 'A1B 2C3'), array(array('weight' => 5, 'count' => 2), array('weight' => 10)));

		$this->assertEquals(
			array(
				array('weight' => 5),
				array('weight' => 5),
				array('weight' => 10),
			),
			$this->packages
		);
	}

	function getRates(array $from, array $to, array $packages)
	{
		$this->from = $from;
		$this->to = $to;
		$this->packages = $packages;

		return array();
	}
}


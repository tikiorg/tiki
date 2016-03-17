<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class TikiVersionTest extends PHPUnit_Framework_TestCase
{
	function versions()
	{
		return array(
			array('9.0', new Tiki_Version_Version(9, 0)),
			array('9.1', new Tiki_Version_Version(9, 1)),
			array('9.1beta2', new Tiki_Version_Version(9, 1, null, 'beta', 2)),
			array('1.9.12.1beta2', new Tiki_Version_Version(1, 9, '12.1', 'beta', 2)),
			array('9.0pre', new Tiki_Version_Version(9, 0, null, 'pre')),
		);
	}

	/**
	 * @dataProvider versions
	 */
	function testParseVersions($string, $version)
	{
		$this->assertEquals($version, Tiki_Version_Version::get($string));
	}

	/**
	 * @dataProvider versions
	 */
	function testWriteVersions($string, $version)
	{
		$this->assertEquals($string, (string) $version);
	}

	function testVerifyLatestVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('9.0');

		$response = $checker->check(
			function ($url) use (& $out)
			{
				$out = $url;
				return <<<O
9.0
8.4
6.7
O;
			}
		);

		$this->assertEquals('http://tiki.org/regular.cycle', $out);
		$this->assertEquals(array(), $response);
	}

	function testVerifyPastSupportedVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.4');

		$response = $checker->check(
			function ($url) use (& $out)
			{
				$out = $url;
				return <<<O
9.0
8.4
6.7
O;
			}
		);

		$this->assertEquals(
			array(
				new Tiki_Version_Upgrade('8.4', '9.0', false),
			), $response
		);
	}

	function testVerifyMinorUpdate()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.2');

		$response = $checker->check(
			function ($url) use (& $out)
			{
				$out = $url;
				return <<<O
9.0
8.4
6.7
O;
			}
		);

		$this->assertEquals(
			array(
				new Tiki_Version_Upgrade('8.2', '8.4', true),
				new Tiki_Version_Upgrade('8.4', '9.0', false),
			), $response
		);
	}

	function testVerifyUpgradePrerelease()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.4beta3');

		$response = $checker->check(
			function ($url) use (& $out)
			{
				$out = $url;
				return <<<O
9.0
8.4
6.7
O;
			}
		);

		$this->assertEquals(
			array(
				new Tiki_Version_Upgrade('8.4beta3', '8.4', true),
				new Tiki_Version_Upgrade('8.4', '9.0', false),
			), $response
		);
	}

	function testUpgradeFromUnsupportedVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('4.3');

		$response = $checker->check(
			function ($url) use (& $out) {
				$out = $url;
				return <<<O
8.4
9.0
6.7
O;
			}
		);

		$this->assertEquals(
			array(
				new Tiki_Version_Upgrade('4.3', '9.0', true),
			), $response
		);
	}

	function testCurrentVersionMoreRecent()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('10.0');

		$response = $checker->check(
			function ($url) use (& $out)
			{
				$out = $url;
				return <<<O
8.4
9.0
6.7
O;
			}
		);

		$this->assertEquals(array(), $response);
	}

	/**
	 * @dataProvider upgradeMessages
	 */
	function testObtainMessages($string, $upgrade)
	{
		$this->assertEquals($string, $upgrade->getMessage());
	}

	function upgradeMessages()
	{
		return array(
			array('Version 8.2 is no longer supported. A minor upgrade to 8.4 is required.', new Tiki_Version_Upgrade('8.2', '8.4', true)),
			array('Version 4.3 is no longer supported. A major upgrade to 9.0 is required.', new Tiki_Version_Upgrade('4.3', '9.0', true)),
			array('Version 8.4 is still supported. However, a major upgrade to 9.0 is available.', new Tiki_Version_Upgrade('8.4', '9.0', false)),
		);
	}
}


<?php

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

	function testVerifyLatestVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('9.0');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
9.0
8.4
6.7
O;
		});

		$this->assertEquals('http://tiki.org/regular.cycle', $out);
		$this->assertNull($response);
	}

	function testVerifyPastSupportedVersionVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.4');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
9.0
8.4
6.7
O;
		});

		$this->assertNull($response);
	}

	function testVerifyMinorUpdate()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.2');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
9.0
8.4
6.7
O;
		});

		$this->assertEquals(new Tiki_Version_Upgrade('8.2', '8.4'), $response);
	}

	function testVerifyUpgradePrerelease()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('8.4beta3');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
9.0
8.4
6.7
O;
		});

		$this->assertEquals(new Tiki_Version_Upgrade('8.4beta3', '8.4'), $response);
	}

	function testUpgradeFromUnsupportedVersion()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('4.3');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
8.4
9.0
6.7
O;
		});

		$this->assertEquals(new Tiki_Version_Upgrade('4.3', '9.0'), $response);
	}

	function testCurrentVersionMoreRecent()
	{
		$checker = new Tiki_Version_Checker;
		$checker->setCycle('regular');
		$checker->setVersion('10.0');

		$response = $checker->check(function ($url) use (& $out) {
			$out = $url;
			return <<<O
8.4
9.0
6.7
O;
		});

		$this->assertNull($response);
	}
}


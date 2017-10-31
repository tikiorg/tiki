<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\Package\ComposerCli;
use org\bovigo\vfs\vfsStream;

/*
 * @group unit
 */

class Tiki_Package_ComposerCliTest extends TikiTestCase
{

	const COMPOSER_JSON_DIST = '{
	"name": "tiki/tiki-custom",
	"description": "Tiki Wiki CMS Groupware",
	"license": "LGPL-2.1",
	"homepage": "https://tiki.org",
	"minimum-stability": "stable",
	"require": {
	},
	"config": {
		"process-timeout": 5000,
		"bin-dir": "bin"
	}
}';

	const SAMPLE_COMPOSER = '{
    "minimum-stability": "stable",
    "config": {
        "process-timeout": 5000,
        "bin-dir": "bin"
    },
    "require": {
        "psr/log": "^1.0"
    }
}';

	const SAMPLE_COMPOSER_BIG = '{
  "name": "tiki/tiki-custom",
  "description": "Tiki Wiki CMS Groupware - composer.json",
  "license": "LGPL-2.1",
  "homepage": "https://tiki.org",
  "minimum-stability": "stable",
  "require": {
    "foo/bar": "^1.0.0",
    "test/unit": "^1.0.0"
  },
  "config": {
    "process-timeout": 5000,
    "bin-dir": "bin"
  }
}';

	protected $root;
	protected $composerCli;

	function setUp()
	{
		parent::setUp();

		$this->root = vfsStream::setup(__CLASS__);
		$this->composerCli = new ComposerCli(vfsStream::url(__CLASS__));
	}

	/**
	 * Test getComposerConfigOrDefault
	 *
	 * @param $composer
	 * @param $composerDist
	 * @param $expected
	 *
	 * @dataProvider getComposerConfigOrDefaultProvider
	 */
	public function testGetComposerConfigOrDefault($composer, $composerDist, $expected)
	{
		$structure = [];
		if (!is_null($composer)) {
			$structure['composer.json'] = $composer;
		}
		if (!is_null($composerDist)) {
			$structure['composer.json.dist'] = $composerDist;
		}

		$stream = uniqid(true);
		vfsStream::setup($stream, null, $structure);

		$composerCli = new ComposerCli(vfsStream::url($stream));

		$this->assertEquals(json_decode($expected, true), $composerCli->getComposerConfigOrDefault());

	}

	public function getComposerConfigOrDefaultProvider()
	{
		return [
			[self::SAMPLE_COMPOSER, self::COMPOSER_JSON_DIST, self::SAMPLE_COMPOSER],
			[null, self::COMPOSER_JSON_DIST, self::COMPOSER_JSON_DIST],
			[null, null, ComposerCli::FALLBACK_COMPOSER_JSON],
		];
	}


	/**
	 * Test getListOfPackagesFromConfig
	 *
	 * @param $composerJson
	 * @param $showResult
	 * @param $expected
	 *
	 * @dataProvider getListOfPackagesFromConfigProvider
	 */
	function testGetListOfPackagesFromConfig($composerJson, $showResult, $expected)
	{
		vfsStream::create(
			[
				'composer.json' => $composerJson,
			],
			$this->root
		);

		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['checkConfigExists', 'canExecuteComposer', 'execShow'])
			->setConstructorArgs([vfsStream::url(__CLASS__)])
			->getMock();

		$composerCli->method('checkConfigExists')
			->willReturn(true);

		$composerCli->method('canExecuteComposer')
			->willReturn(true);

		$composerCli->method('execShow')
			->willReturn($showResult);

		$this->assertEquals($expected, $composerCli->getListOfPackagesFromConfig());
	}

	public function getListOfPackagesFromConfigProvider()
	{
		return [
			[ // no package installed, no key
				self::SAMPLE_COMPOSER_BIG,
				[],
				[
					[
						'name' => 'foo/bar',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
					[
						'name' => 'test/unit',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
				],
			],
			[ // no package installed, with key
				self::SAMPLE_COMPOSER_BIG,
				['installed' => []],
				[
					[
						'name' => 'foo/bar',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
					[
						'name' => 'test/unit',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
				],
			],
			[ // one package installed
				self::SAMPLE_COMPOSER_BIG,
				['installed' => [['name' => 'foo/bar', 'version' => '1.2.3']]],
				[
					[
						'name' => 'foo/bar',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
					[
						'name' => 'test/unit',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
				],
			],
			[ // all packages installed
				self::SAMPLE_COMPOSER_BIG,
				['installed' => [['name' => 'foo/bar', 'version' => '1.2.3'], ['name' => 'test/unit', 'version' => '1.4.5']]],
				[
					[
						'name' => 'foo/bar',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
					[
						'name' => 'test/unit',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.4.5',
					],
				],
			],
			[ // packages installed that are not in composer.json
				self::SAMPLE_COMPOSER_BIG,
				['installed' => [['name' => 'foo/bar2', 'version' => '1.2.3']]],
				[
					[
						'name' => 'foo/bar',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
					[
						'name' => 'test/unit',
						'status' => 'missing',
						'required' => '^1.0.0',
						'installed' => '',
					],
				],
			],
			[ // all packages installed, case mismatch
				self::SAMPLE_COMPOSER_BIG,
				[
					'installed' => [
						['name' => 'FOO/BAR', 'version' => '1.2.3'],
						['name' => 'TesT/UniT', 'version' => '1.4.5'],
					],
				],
				[
					[
						'name' => 'foo/bar',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
					[
						'name' => 'test/unit',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.4.5',
					],
				],
			],
		];
	}

	/**
	 * Test addComposerPackageToJson
	 * @dataProvider addComposerPackageToJsonProvider
	 */
	function testAddComposerPackageToJson($composerJson, $package, $version, $scripts, $expected)
	{

		$result = $this->composerCli->addComposerPackageToJson(
			$composerJson,
			$package,
			$version,
			$scripts
		);

		$this->assertEquals($expected, $result);
	}

	public function addComposerPackageToJsonProvider()
	{

		$returnDataProvider = [];

		$composerJson = json_decode(self::SAMPLE_COMPOSER, true);

		// simple test
		$expected = $composerJson;
		$expected['require']['package'] = 'version';
		$returnDataProvider[] = [
			$composerJson,
			'package',
			'version',
			null,
			$expected,
		];

		// empty JSON
		$expected = ['require' => ['package' => 'version']];
		$returnDataProvider[] = [
			'',
			'package',
			'version',
			null,
			$expected,
		];

		// require existing package
		$returnDataProvider[] = [
			$composerJson,
			'psr/log',
			'^1.0',
			null,
			$composerJson,
		];

		// Add all scripts
		$expected = $composerJson;
		$expected['require']['foo/bar'] = '^1.0';
		$expected['scripts'] = [
			'pre-install-cmd' => ['FooBarInstaller\Installer::install'],
			'pre-update-cmd' => ['FooBarInstaller\Installer::install'],
			'post-install-cmd' => ['FooBarInstaller\Installer::install'],
			'post-update-cmd' => ['FooBarInstaller\Installer::install'],
		];
		$returnDataProvider[] = [
			$composerJson,
			'foo/bar',
			'^1.0',
			[
				'pre-install-cmd' => ['FooBarInstaller\Installer::install'],
				'pre-update-cmd' => ['FooBarInstaller\Installer::install'],
				'post-install-cmd' => ['FooBarInstaller\Installer::install'],
				'post-update-cmd' => ['FooBarInstaller\Installer::install'],
			],
			$expected,
		];

		// Append Script
		$source = $composerJson;
		$source['scripts'] = [
			'pre-install-cmd' => ['SomeInstaller\Installer::install'],
		];
		$expected = $composerJson;
		$expected['require']['foo/bar'] = '^1.0';
		$expected['scripts'] = [
			'pre-install-cmd' => ['SomeInstaller\Installer::install', 'FooBarInstaller\Installer::install'],
		];
		$returnDataProvider[] = [
			$source,
			'foo/bar',
			'^1.0',
			[
				'pre-install-cmd' => ['FooBarInstaller\Installer::install'],
			],
			$expected,
		];

		return $returnDataProvider;
	}
}

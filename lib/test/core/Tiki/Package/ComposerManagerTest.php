<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Tiki\Package\ComposerManager;
use org\bovigo\vfs\vfsStream;


class Tiki_Package_ComposerManagerTest extends TikiTestCase
{

	protected $root;
	protected $rootPath;

	/** @var  ComposerManager */
	protected $composerManager;

	function setUp()
	{
		parent::setUp();

		$this->root = vfsStream::setup(__CLASS__);
		$this->rootPath = vfsStream::url(__CLASS__);

		$this->composerManager = new ComposerManager(
			$this->rootPath,
			null,
			null,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);
	}

	function testGetComposer()
	{
		$this->assertInstanceOf('Tiki\Package\ComposerCli', $this->composerManager->getComposer());
	}

	function testComposerPath()
	{
		$this->assertEquals($this->composerManager->composerPath(), $this->rootPath . '/temp/composer.phar');
	}

	function testBrokenYaml()
	{

		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['getListOfPackagesFromConfig'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli->method('getListOfPackagesFromConfig')
			->willReturn([]);

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackagesBroken.yml'
		);

		$response = $composerManager->getAvailable();

		$this->assertCount(0, $response);
	}

	function testIfNoPackageIsInstalledAllAreAvailable()
	{

		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['getListOfPackagesFromConfig'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli->method('getListOfPackagesFromConfig')
			->willReturn([]);

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$response = $composerManager->getAvailable();
		$nameOfPackages = array_column($response, 'name');

		$this->assertContains('jerome-breton/casperjs-installer', $nameOfPackages);
		$this->assertContains('enygma/expose', $nameOfPackages);
	}

	function testPackageNotAvailableIfInstalled()
	{

		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['getListOfPackagesFromConfig'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli->method('getListOfPackagesFromConfig')
			->willReturn(
				[
					[
						'name' => 'jerome-breton/casperjs-installer',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
				]
			);

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$response = $composerManager->getAvailable();
		$nameOfPackages = array_column($response, 'name');

		$this->assertNotContains('jerome-breton/casperjs-installer', $nameOfPackages);
		$this->assertContains('enygma/expose', $nameOfPackages);
	}

	function testAllPackagesAvailableIfNotFiltered()
	{

		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['getListOfPackagesFromConfig'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli->method('getListOfPackagesFromConfig')
			->willReturn(
				[
					[
						'name' => 'jerome-breton/casperjs-installer',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
				]
			);

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$response = $composerManager->getAvailable(false);
		$nameOfPackages = array_column($response, 'name');

		$this->assertContains('jerome-breton/casperjs-installer', $nameOfPackages);
		$this->assertContains('enygma/expose', $nameOfPackages);
	}

	function testInstallNotExistingPackage()
	{
		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['installPackage'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$this->assertNull($composerManager->installPackage('FooBar'));
	}

	function testInstallPackage()
	{
		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['canExecuteComposer', 'installMissingPackages'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli
			->expects($this->once())
			->method('canExecuteComposer')
			->willReturn(true);

		$composerCli
			->expects($this->once())
			->method('installMissingPackages')
			->willReturn('__PACKAGE__INSTALLED__');

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$this->assertRegexp('/__PACKAGE__INSTALLED__/', $composerManager->installPackage('CasperJS'));

		$this->assertJsonFileEqualsJsonFile(
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'CasperJsComposer.json',
			$this->rootPath . '/composer.json'
		);
	}

	function testGetInstalled()
	{
		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['getListOfPackagesFromConfig'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli
			->expects($this->once())
			->method('getListOfPackagesFromConfig')
			->willReturn(
				[
					[
						'name' => 'jerome-breton/casperjs-installer',
						'status' => 'installed',
						'required' => '^1.0.0',
						'installed' => '1.2.3',
					],
					[
						'name' => 'Foo/Bar',
						'status' => 'installed',
						'required' => '^2.0.0',
						'installed' => '2.2.3',
					],
				]
			);

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$response = $composerManager->getInstalled();

		$this->assertCount(2, $response);

		$this->assertEquals('CasperJS', $response[0]['key']);
		$this->assertEquals('jerome-breton/casperjs-installer', $response[0]['name']);

		$this->assertEquals('', $response[1]['key']);
		$this->assertEquals('Foo/Bar', $response[1]['name']);

	}

	function testRemoveUnknownPackageFails()
	{
		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['removePackage'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli
			->expects($this->never())
			->method('removePackage');

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$this->assertNull($composerManager->removePackage('FooBar'));
	}

	function testRemovePackage()
	{
		$composerCli = $this->getMockBuilder('Tiki\Package\ComposerCli')
			->setMethods(['removePackage'])
			->setConstructorArgs([$this->rootPath])
			->getMock();

		$composerCli
			->expects($this->once())
			->method('removePackage')
			->willReturn('__PACKAGE__REMOVED__');

		$composerManager = new ComposerManager(
			$this->rootPath,
			null,
			$composerCli,
			__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'ComposerPackages.yml'
		);

		$this->assertEquals('__PACKAGE__REMOVED__', $composerManager->removePackage('CasperJS'));
	}
}

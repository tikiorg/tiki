<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/CollectFiles.php');
require_once('lib/language/WriteFile.php');
require_once('lib/language/GetStrings.php');
require_once('lib/language/FileType.php');
require_once('lib/language/FileType/Php.php');
require_once('lib/language/FileType/Tpl.php');

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class Language_GetStringsTest extends TikiTestCase
{
	protected $collectFiles;

	protected $fileType;

	protected $obj;

	protected function setUp()
	{
		$this->baseDir = __DIR__ . '/../../../';
		$this->collectFiles = $this->getMock('Language_CollectFiles');
		$this->fileType = $this->getMock('Language_FileType_Php');
		$this->writeFileFactory = $this->getMock('Language_WriteFile_Factory', array());
		$this->writeFile = $this->getMock('WriteFile', array('writeStringsToFile'), array(), '', false);
		$this->obj = new Language_GetStrings($this->collectFiles, $this->writeFileFactory, array('baseDir' => $this->baseDir));
	}

	public function testConstruct_shouldRaiseExceptionForInvalidBaseDir()
	{
		$this->setExpectedException('Language_Exception');
		$this->obj = new Language_GetStrings($this->collectFiles, $this->writeFileFactory, array('baseDir' => 'invalidDir'));
	}

	public function testAddFileType()
	{
		$php = $this->getMock('Language_FileType_Php');
		$php->expects($this->once())->method('getExtensions')->will($this->returnValue(array('.php')));

		$tpl = $this->getMock('Language_FileType_Tpl');
		$tpl->expects($this->once())->method('getExtensions')->will($this->returnValue(array('.tpl')));

		$this->obj->addFileType($php);
		$this->obj->addFileType($tpl);

		$this->assertEquals(array('.php', '.tpl'), $this->obj->getExtensions());
		$this->assertEquals(array($php, $tpl), $this->obj->getFileTypes());
	}

	public function testAddFileType_shouldRaiseExceptionIfSameTypeIsAddedMoreThanOnce()
	{
		$this->setExpectedException('Language_Exception');

		$php = $this->getMock('Language_FileType_Php');
		$php->expects($this->exactly(1))->method('getExtensions')->will($this->returnValue(array('.php')));

		$this->obj->addFileType($php);
		$this->obj->addFileType($php);
	}

	public function testCollectStrings_shouldRaiseExceptionIfEmptyFileTypes()
	{
		$this->setExpectedException('Language_Exception');
		$this->obj->collectStrings('file.php');
	}

	public function testCollectStrings_shouldRaiseExceptionIfInvalidFileExtension()
	{
		$this->setExpectedException('Language_Exception');
		$this->fileType->expects($this->any())->method('getExtensions')->will($this->returnValue(array('.php')));
		$this->obj->addFileType($this->fileType);
		$this->obj->collectStrings('file.');
	}

	public function testCollectStrings_withFileTypePhp()
	{
		$this->obj->addFileType(new Language_FileType_Php);
		$strings = $this->obj->collectStrings(__DIR__ . '/fixtures/test_collecting_strings.php');

		$expectedResult = array('%0 enabled', '%0 disabled', 'Features', 'Enable/disable Tiki features here, but configure them elsewhere',
			'General', 'General preferences and settings', 'Login', 'User registration, login and authentication', 'Wiki', 'Wiki settings',
			'Help on $admintitle Config', "Congratulations!\n\nYour server can send emails.\n\n",
		);

		$this->assertEquals($expectedResult, $strings);
	}

	public function testCollectStrings_shouldCallRegexPostProcessMethodIfOneExists()
	{
		$php = $this->getMock('Language_FileType_Php', array('getExtensions', 'getCleanupRegexes', 'singleQuoted', 'doubleQuoted'));
		$php->expects($this->exactly(2))->method('getExtensions')->will($this->returnValue(array('.php')));
		$php->expects($this->exactly(1))->method('getCleanupRegexes')->will($this->returnValue(array()));
		$php->expects($this->exactly(1))->method('singleQuoted')->will($this->returnValue(array(0 => array(), 1 => array())));
		$php->expects($this->exactly(1))->method('doubleQuoted')->will($this->returnValue(array(0 => array(), 1 => array())));

		$this->obj->addFileType($php);
		$this->obj->addFileType(new Language_FileType_Tpl);
		$this->obj->collectStrings(__DIR__ . '/fixtures/test_collecting_strings.php');
	}

	public function testCollectStrings_withFileTypeTpl()
	{
		$this->obj->addFileType(new Language_FileType_Tpl);
		$strings = $this->obj->collectStrings(__DIR__ . '/fixtures/test_collecting_strings.tpl');

		$expectedResult = array('Bytecode Cache', 'Using <strong>%0</strong>.These stats affect all PHP applications running on the server.',
			'Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache.',
			'Used', 'Available', 'Memory', 'Hit', 'Miss', 'Cache Hits', 'Few hits recorded. Statistics may not be representative.',
			'Low hit ratio. %0 may be misconfigured and not used.',
			'Bytecode cache is not used. Using a bytecode cache (APC, XCache) is highly recommended for production environments.', 'Errors', 'Errors:', 'Created',
		);

		$this->assertEquals($expectedResult, $strings);
	}

	public function testCollectString_shouldNotConsiderEmptyCallsToTra()
	{
		$this->obj->addFileType(new Language_FileType_Php);

		$fileName = 'file1.php';

		$root = vfsStream::setup('root');

		$file = new vfsStreamFile($fileName);
		$file->setContent(
			"'sub' => array(
			       'required' => false,
			       'default' => 'n',
			       'filter' => 'alpha',
			       'options' => array(
			           array('text' => tra(''), 'value' => ''),
			           array('text' => tra('Yes'), 'value' => 'y'),
			           array('text' => tra('No'), 'value' => 'n')
			       ),
			   ),"
		);

		$root->addChild($file);

		$expectedResult = array('Yes', 'No');

		$this->assertEquals($expectedResult, $this->obj->collectStrings(vfsStream::url('root/' . $fileName)));
	}

	public function testRun_shouldRaiseExceptionIfEmptyFileTypes()
	{
		$this->setExpectedException('Language_Exception');
		$this->obj->run();
	}

	public function testRun_shouldReturnCollectedStrings()
	{
		$files = array('file1', 'file2', 'file3');

		$strings = array(
			'string1' => array('name' => 'string1'),
			'string2' => array('name' => 'string2'),
			'string3' => array('name' => 'string3'),
			'string4' => array('name' => 'string4'),
		);

		$this->collectFiles->expects($this->exactly(1))->method('setExtensions');
		$this->collectFiles->expects($this->exactly(1))->method('run')->with($this->baseDir)->will($this->returnValue($files));

		$obj = $this->getMock('Language_GetStrings', array('collectStrings', 'writeToFiles'), array($this->collectFiles, $this->writeFileFactory, array('baseDir' => $this->baseDir)));

		$obj->expects($this->exactly(1))->method('writeToFiles')->with($strings);
		$obj->expects($this->at(0))->method('collectStrings')->with('file1')->will($this->returnValue(array('string1', 'string2')));
		$obj->expects($this->at(1))->method('collectStrings')->with('file2')->will($this->returnValue(array('string2', 'string3')));
		$obj->expects($this->at(2))->method('collectStrings')->with('file3')->will($this->returnValue(array('string3', 'string4')));

		$this->fileType->expects($this->exactly(1))->method('getExtensions')->will($this->returnValue(array('.php')));
		$obj->addFileType($this->fileType);

		$this->assertNull($obj->run());
	}

	public function testSetLanguages_shouldSetLanguagesForArrayParam()
	{
		$languages = array('en', 'es', 'pt-br');
		$this->obj->setLanguages($languages);
		$this->assertEquals($this->obj->getLanguages(), $languages);
	}

	public function testSetLanguages_shouldSetLanguagesForStringParam()
	{
		$language = 'en';
		$this->obj->setLanguages($language);
		$this->assertEquals($this->obj->getLanguages(), array($language));
	}

	public function testSetLanguages_shouldRaiseExceptionForInvalidLanguage()
	{
		$languages = array('en', 'invalid');
		$this->setExpectedException('Language_Exception');
		$this->obj->setLanguages($languages);
	}

	public function testSetLanguages_shouldCallGetAllLanguagesIfLanguageParamIsNull()
	{
		$obj = $this->getMock('Language_GetStrings', array('getAllLanguages'), array($this->collectFiles, $this->writeFileFactory));
		$obj->expects($this->once())->method('getAllLanguages');
		$obj->setLanguages();
	}

	public function testWriteToFiles_shouldCallWriteStringsThreeTimes()
	{
		$strings = array('string1', 'string2', 'string3', 'string4');

		$this->writeFile->expects($this->exactly(3))->method('writeStringsToFile')->with($strings, false);

		$this->obj->setLanguages(array('en', 'es', 'pt-br'));

		$this->writeFileFactory->expects($this->at(0))->method('factory')
			->will($this->returnValue($this->writeFile))
			->with($this->stringContains('en/language.php'));

		$this->writeFileFactory->expects($this->at(1))->method('factory')
			->will($this->returnValue($this->writeFile))
			->with($this->stringContains('es/language.php'));

		$this->writeFileFactory->expects($this->at(2))->method('factory')
			->will($this->returnValue($this->writeFile))
			->with($this->stringContains('pt-br/language.php'));

		$this->obj->writeToFiles($strings);
	}

	public function testWriteToFiles_shouldCallWriteStringsWithOutputFileParam()
	{
		$strings = array('string1', 'string2', 'string3', 'string4');

		$this->writeFile->expects($this->atLeastOnce())->method('writeStringsToFile')->with($strings, true);
		$this->writeFileFactory->expects($this->atLeastOnce())->method('factory')->will($this->returnValue($this->writeFile));

		$obj = new Language_GetStrings($this->collectFiles, $this->writeFileFactory, array('outputFiles' => true, 'baseDir' => $this->baseDir));

		$obj->writeToFiles($strings);
	}

	public function testWriteToFiles_shouldUseCustomFileName()
	{
		$strings = array('string1', 'string2', 'string3', 'string4');

		$this->writeFile->expects($this->once())->method('writeStringsToFile')->with($strings, false);
		$this->writeFileFactory->expects($this->once())->method('factory')->with($this->stringContains('language_r.php'))->will($this->returnValue($this->writeFile));

		$obj = new Language_GetStrings($this->collectFiles, $this->writeFileFactory, array('baseDir' => $this->baseDir, 'lang' => 'es', 'fileName' => 'language_r.php'));
		$obj->writeToFiles($strings);
	}

	public function testScanFiles_shouldReturnStringsFromFiles()
	{
		$files = array('file1', 'file2', 'file3');

		$strings = array(
			'string1' => array('name' => 'string1'),
			'string2' => array('name' => 'string2'),
			'string3' => array('name' => 'string3'),
			'string4' => array('name' => 'string4'),
		);

		$obj = $this->getMock('Language_GetStrings', array('collectStrings', 'setLanguages'), array($this->collectFiles, $this->writeFileFactory));

		$obj->expects($this->at(0))->method('collectStrings')->with('file1')->will($this->returnValue(array('string1', 'string2')));
		$obj->expects($this->at(1))->method('collectStrings')->with('file2')->will($this->returnValue(array('string2', 'string3')));
		$obj->expects($this->at(2))->method('collectStrings')->with('file3')->will($this->returnValue(array('string3', 'string4')));

		$this->assertEquals($strings, $obj->scanFiles($files));
	}

	public function testScanFiles_shouldReturnInformationAboutTheFilesWhereTheStringsWereFound()
	{
		$files = array('file1', 'file2', 'file3');

		$strings = array(
			'string1' => array('name' => 'string1', 'files' => array('file1')),
			'string2' => array('name' => 'string2', 'files' => array('file1', 'file2')),
			'string3' => array('name' => 'string3', 'files' => array('file2', 'file3')),
			'string4' => array('name' => 'string4', 'files' => array('file3')),
		);

		$obj = $this->getMock('Language_GetStrings', array('collectStrings', 'setLanguages'), array($this->collectFiles, $this->writeFileFactory, array('outputFiles' => true)));

		$obj->expects($this->at(0))->method('collectStrings')->with('file1')->will($this->returnValue(array('string1', 'string2')));
		$obj->expects($this->at(1))->method('collectStrings')->with('file2')->will($this->returnValue(array('string2', 'string3')));
		$obj->expects($this->at(2))->method('collectStrings')->with('file3')->will($this->returnValue(array('string3', 'string4')));

		$this->assertEquals($strings, $obj->scanFiles($files));
	}
}

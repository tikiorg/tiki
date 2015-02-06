<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/language/CollectFiles.php';
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamDirectory;

class Language_CollectFilesTest extends TikiTestCase
{
	protected function setUp()
	{
		$this->obj = new Language_CollectFiles;

		// setup a mock filesystem with directories and files
		$root = vfsStream::setup('root');
		$dir1 = new vfsStreamDirectory('dir1');
		$dir2 = new vfsStreamDirectory('dir2');
		$root->addChild($dir1);
		$root->addChild($dir2);

		$dir1->addChild(new vfsStreamFile('file1.tpl'));
		$dir2->addChild(new vfsStreamFile('file2.php'));
		$dir2->addChild(new vfsStreamFile('file3.php'));
		$dir2->addChild(new vfsStreamFile('file4.txt'));
	}

	public function testSetExcludeDirs_shouldRaiseExceptionForInvalidDir()
	{
		$dirs = array('invalidDir');
		$this->setExpectedException('Language_Exception');
		$this->obj->setExcludeDirs($dirs);
	}

	public function testSetExcludeDirsAndGetExcludeDir_shouldSetProperty()
	{
		$dir = 'language/fixtures';
		$dirs = array($dir);
		$expectedResult = array(getcwd() . '/' . $dir);
		$this->obj->setExcludeDirs($dirs);
		$this->assertEquals($expectedResult, $this->obj->getExcludeDirs());
	}

	public function testIncludeFilesDirs_shouldRaiseExceptionForInvalidFile()
	{
		$dirs = array('invalidFile');
		$this->setExpectedException('Language_Exception');
		$this->obj->setIncludeFiles($dirs);
	}

	public function testIncludeFilesDirsAndGetIncludeFiles_shouldSetProperty()
	{
		$dirs = array(__DIR__ . '/fixtures');
		$this->obj->setIncludeFiles($dirs);
		$this->assertEquals($dirs, $this->obj->getIncludeFiles());
	}

	public function testRun_shouldMergeArrays()
	{
		$obj = $this->getMock('Language_CollectFiles', array('scanDir', 'getIncludeFiles'));
		$obj->expects($this->once())->method('scanDir')->will($this->returnValue(array('lib/test.php', 'tiki-test.php')));
		$obj->expects($this->once())->method('getIncludeFiles')->will($this->returnValue(array('tiki-test.php', 'tiki-index.php')));

		$this->assertEquals(array('lib/test.php', 'tiki-test.php', 'tiki-index.php'), $obj->run('.'));
	}

	public function testScanDir_shouldRaiseExceptionForInvalidDir()
	{
		$this->setExpectedException('Language_Exception');
		$this->obj->scanDir('invalidDir');
	}

	public function testScanDir_shouldReturnFiles()
	{
		$expectedResult = array('vfs://root/dir1/file1.tpl', 'vfs://root/dir2/file2.php', 'vfs://root/dir2/file3.php');
		$this->assertEquals($expectedResult, $this->obj->scanDir(vfsStream::url('root')));
	}

	public function testScanDir_shouldIgnoreExcludedDirs()
	{
		$obj = $this->getMock('Language_CollectFiles', array('getExcludeDirs'));
		$obj->expects($this->exactly(5))->method('getExcludeDirs')->will($this->returnValue(array('vfs://root/dir1')));
		$expectedResult = array('vfs://root/dir2/file2.php', 'vfs://root/dir2/file3.php');
		$this->assertEquals($expectedResult, $obj->scanDir(vfsStream::url('root')));
	}

	public function testScanDir_shouldAcceptIncludedFiles()
	{
		$obj = $this->getMock('Language_CollectFiles', array('getExcludeDirs', 'getIncludeFiles'));
		$obj->expects($this->exactly(3))->method('getExcludeDirs')->will($this->returnValue(array('vfs://root/dir2')));
		$obj->expects($this->exactly(2))->method('getIncludeFiles')->will($this->returnValue(array('vfs://root/dir2/file3.php')));

		$expectedResult = array('vfs://root/dir1/file1.tpl', 'vfs://root/dir2/file3.php');
		$this->assertEquals($expectedResult, $obj->scanDir(vfsStream::url('root')));
	}
}

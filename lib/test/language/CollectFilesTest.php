<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		$dirs = ['invalidDir'];
		$this->expectException('Language_Exception');
		$this->obj->setExcludeDirs($dirs);
	}

	public function testSetExcludeDirsAndGetExcludeDir_shouldSetProperty()
	{
		$dirs = ['fixtures'];
		$cwd = getcwd();
		chdir(__DIR__);
		$expectedResult = [getcwd() . '/' . 'fixtures'];
		$this->obj->setExcludeDirs($dirs);
		$this->assertEquals($expectedResult, $this->obj->getExcludeDirs());
		chdir($cwd);
	}

	public function testIncludeFilesDirs_shouldRaiseExceptionForInvalidFile()
	{
		$dirs = ['invalidFile'];
		$this->expectException('Language_Exception');
		$this->obj->setIncludeFiles($dirs);
	}

	public function testIncludeFilesDirsAndGetIncludeFiles_shouldSetProperty()
	{
		$dirs = [__DIR__ . '/fixtures'];
		$this->obj->setIncludeFiles($dirs);
		$this->assertEquals($dirs, $this->obj->getIncludeFiles());
	}

	public function testRun_shouldMergeArrays()
	{
		$obj = $this->getMockBuilder('Language_CollectFiles')
					->setMethods(['scanDir', 'getIncludeFiles'])
					->getMock();
		$obj->expects($this->once())->method('scanDir')->will($this->returnValue(['lib/test.php', 'tiki-test.php']));
		$obj->expects($this->once())->method('getIncludeFiles')->will($this->returnValue(['tiki-test.php', 'tiki-index.php']));

		$this->assertEquals(['lib/test.php', 'tiki-test.php', 'tiki-index.php'], $obj->run('.'));
	}

	public function testScanDir_shouldRaiseExceptionForInvalidDir()
	{
		$this->expectException('Language_Exception');
		$this->obj->scanDir('invalidDir');
	}

	public function testScanDir_shouldReturnFiles()
	{
		$expectedResult = ['vfs://root/dir1/file1.tpl', 'vfs://root/dir2/file2.php', 'vfs://root/dir2/file3.php'];
		$this->assertEquals($expectedResult, $this->obj->scanDir(vfsStream::url('root')));
	}

	public function testScanDir_shouldIgnoreExcludedDirs()
	{
		$obj = $this->getMockBuilder('Language_CollectFiles')
					->setMethods(['getExcludeDirs'])
					->getMock();

		$obj->expects($this->exactly(5))->method('getExcludeDirs')->will($this->returnValue(['vfs://root/dir1']));
		$expectedResult = ['vfs://root/dir2/file2.php', 'vfs://root/dir2/file3.php'];
		$this->assertEquals($expectedResult, $obj->scanDir(vfsStream::url('root')));
	}

	public function testScanDir_shouldAcceptIncludedFiles()
	{
		$obj = $this->getMockBuilder('Language_CollectFiles')
					->setMethods(['getExcludeDirs', 'getIncludeFiles'])
					->getMock();

		$obj->expects($this->exactly(3))->method('getExcludeDirs')->will($this->returnValue(['vfs://root/dir2']));
		$obj->expects($this->exactly(2))->method('getIncludeFiles')->will($this->returnValue(['vfs://root/dir2/file3.php']));

		$expectedResult = ['vfs://root/dir1/file1.tpl', 'vfs://root/dir2/file3.php'];
		$this->assertEquals($expectedResult, $obj->scanDir(vfsStream::url('root')));
	}
}

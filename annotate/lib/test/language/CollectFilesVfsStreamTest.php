<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/CollectFiles.php');
@include_once('vfsStream/vfsStream.php');

/**
 * Class for tests methods that uses vfsStream, if available,
 * to mock the filesystem.
 */
class Language_CollectFiles_VfsStream_Test extends TikiTestCase
{
	protected $obj;
	
	protected function setUp()
	{
		if (!class_exists('vfsStream')) {
			$this->markTestSkipped('vfsStream class not available');
		}

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

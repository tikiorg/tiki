<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/language/CollectFiles.php';

class Language_CollectFilesTest extends TikiTestCase
{
	protected function setUp()
	{
		$this->obj = new Language_CollectFiles;
	}
	
	public function testSetExcludeDirs_shouldRaiseExceptionForInvalidDir() {
		$dirs = array('invalidDir');
		$this->setExpectedException('Language_Exception');
		$this->obj->setExcludeDirs($dirs);
	}
	
	public function testSetExcludeDirsAndGetExcludeDir_shouldSetProperty() {
		$dirs = array(__DIR__ . '/fixtures');
		$this->obj->setExcludeDirs($dirs);
		$this->assertEquals($dirs, $this->obj->getExcludeDirs());
	}
	
	public function testIncludeFilesDirs_shouldRaiseExceptionForInvalidFile() {
		$dirs = array('invalidFile');
		$this->setExpectedException('Language_Exception');
		$this->obj->setIncludeFiles($dirs);
	}
	
	public function testIncludeFilesDirsAndGetIncludeFiles_shouldSetProperty() {
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
}
<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/Exception.php');
require_once('lib/language/WriteFile.php');
include_once('vfsStream/vfsStream.php');

class Language_WriteFileTest extends TikiTestCase
{
	protected $obj;
	
	protected function setUp()
	{
		if (!class_exists('vfsStream')) {
			$this->markTestSkipped('vfsStream class not available');
		}
		
		// setup a mock filesystem 
		$lang = vfsStream::setup('lang');
		$lang->addChild(new vfsStreamFile('language.php'));

		$this->filePath = vfsStream::url('lang/language.php');
		
		$this->obj = new Language_WriteFile;
	}

	public function testWriteStringsToFile_shouldRaiseExceptionForInvalidFile()
	{
		$strings = array('string');
		$this->setExpectedException('Language_Exception');
		$this->obj->writeStringsToFile($strings, vfsStream::url('lang/invalidFile'));
	}
	
	public function testWriteStringsToFile_shouldReturnFalseIfEmptyParam()
	{
		$this->assertFalse($this->obj->writeStringsToFile(array(), $this->filePath));
	}
	
	public function testWriteStringsToFile_shouldWriteSimpleStrings()
	{
		$strings = array('First string', 'Second string', 'etc');
		$this->obj->writeStringsToFile($strings, $this->filePath);
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_simple.php'), file_get_contents($this->filePath));
	}

	public function testWriteStringsToFile_shouldKeepTranslationsEvenIfTheyAreEqualToEnglishString()
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
		
		$strings = array('First string', 'Second string', 'Used string', 'Translation is the same as English string', 'etc');
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}
	
	public function testWriteStringsToFile_shouldIgnoreUnusedStrings()
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
		
		$strings = array('First string', 'Second string', 'Used string', 'Translation is the same as English string', 'etc');
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}
}
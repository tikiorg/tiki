<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/Exception.php');
require_once('lib/language/WriteFile.php');
@include_once('vfsStream/vfsStream.php');

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
		$string1 = new stdClass;
		$string1->name = 'First string';
		
		$string2 = new stdClass;
		$string2->name = 'Second string';
		
		$string3 = new stdClass;
		$string3->name = 'etc';
		
		$strings = array($string1->name => $string1, $string2->name => $string2, $string3->name => $string3);
		
		$this->obj->writeStringsToFile($strings, $this->filePath);
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_simple.php'), file_get_contents($this->filePath));
	}

	public function writeStringsToFile_provider()
	{
		$string1 = new stdClass;
		$string1->name = 'First string';
		$string1->files = array('file1', 'file3');
		
		$string2 = new stdClass;
		$string2->name = 'Second string';
		$string2->files = array('file2');
		
		$string3 = new stdClass;
		$string3->name = 'Used string';
		$string3->files = array('file3');
		
		$string4 = new stdClass;
		$string4->name = 'Translation is the same as English string';
		$string4->files = array('file5', 'file1');
		
		$string5 = new stdClass;
		$string5->name = 'etc';
		$string5->files = array('file4');
		
		$strings = array($string1->name => $string1, $string2->name => $string2, $string3->name => $string3, $string4->name => $string4, $string5->name => $string5);
		
		return array(array($strings));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldKeepTranslationsEvenIfTheyAreEqualToEnglishString($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
				
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldIgnoreUnusedStrings($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
				
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldUnsetTranslationFromStringObjects($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'First string' => 'Some translation',
			)
		));
		
		$obj->writeStringsToFile($strings, $this->filePath);
		$this->assertFalse(isset($strings['First string']->translation));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldOutputFileWhereStringsWasFound($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
				
		$obj->writeStringsToFile($strings, $this->filePath, true);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations_and_file_paths.php'), file_get_contents($this->filePath));
	}
}

<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WriteFileTest.php 37698 2011-09-26 18:10:35Z sampaioprimo $

require_once('lib/language/Exception.php');
require_once('lib/language/WriteFile.php');
require_once('vfsStream/vfsStream.php');

class Language_WriteFileTest extends TikiTestCase
{
	protected $obj;
	
	protected function setUp()
	{
		// setup a mock filesystem 
		$lang = vfsStream::setup('lang');
		$this->langFile = new vfsStreamFile('language.php');
		$lang->addChild($this->langFile);

		$this->filePath = vfsStream::url('lang/language.php');
		
		$this->obj = new Language_WriteFile;
	}

	public function testWriteStringsToFile_shouldRaiseExceptionForInvalidFile()
	{
		$strings = array(
			'First string' => array('name' => 'First string'),
		);
		$this->setExpectedException('Language_Exception');
		$this->obj->writeStringsToFile($strings, vfsStream::url('lang/invalidFile'));
	}
	
	public function testWriteStringsToFile_shouldReturnFalseIfEmptyParam()
	{
		$this->assertFalse($this->obj->writeStringsToFile(array(), $this->filePath));
	}
	
	public function testWriteStringsToFile_shouldRaiseExceptionIfFileIsNotWritable()
	{
		$strings = array(
			'First string' => array('name' => 'First string'),
		);
		$this->langFile->chmod(0444);
		$this->setExpectedException('Language_Exception');
		$this->obj->writeStringsToFile($strings, vfsStream::url('lang/language.php'));
	}
	
	public function testWriteStringsToFile_shouldWriteSimpleStrings()
	{
		$obj = $this->getMock('Language_WriteFile', array('fileHeader'));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$strings = array(
			'First string' => array('name' => 'First string'),
			'Second string' => array('name' => 'Second string'),
			'etc' => array('name' => 'etc'),
		);
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		// check if a backup of old language file (in this case an empty file) was created 
		$this->assertTrue(file_exists($this->filePath . '.old'));
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_simple.php'), file_get_contents($this->filePath));
	}

	public function writeStringsToFile_provider()
	{
		$strings = array(
			'First string' => array('name' => 'First string', 'files' => array('file1', 'file3')),
			'Second string' => array('name' => 'Second string', 'files' => array('file2')),
			'Used string' => array('name' => 'Used string', 'files' => array('file3')),
			'Translation is the same as English string' => array('name' => 'Translation is the same as English string', 'files' => array('file5', 'file1')),
			'etc' => array('name' => 'etc', 'files' => array('file4')),
		);
		
		return array(array($strings));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldKeepTranslationsEvenIfTheyAreEqualToEnglishString($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
				
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldIgnoreUnusedStrings($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
				
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
		$this->assertFalse(isset($strings['First string']['translation']));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldOutputFileWhereStringsWasFound($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
			)
		));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
				
		$obj->writeStringsToFile($strings, $this->filePath, true);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations_and_file_paths.php'), file_get_contents($this->filePath));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldConsiderStringsWithPunctuationInEndASpecialCase($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
				'Login' => 'Another translation',
				'Add user:' => 'Translation',
			)
		));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
		
		$strings['Login:'] = array('name' => 'Login:');
		$strings['Add user:'] = array('name' => 'Add user:');
		$strings['All users:'] = array('name' => 'All users:');
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_punctuations.php'), file_get_contents($this->filePath));
	}
	
	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldProperlyHandleSpecialCharactersInsideStrings($strings)
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->exactly(1))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Unused string' => 'Some translation',
				'Used string' => 'Another translation',
				'Translation is the same as English string' => 'Translation is the same as English string',
				"Congratulations!\n\nYour server can send emails.\n\n" => "Gratulation!\n\nDein Server kann Emails senden.\n\n",
			)
		));
		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
		
		$strings["Congratulations!\n\nYour server can send emails.\n\n"] = array('name' => "Congratulations!\n\nYour server can send emails.\n\n");
		$strings['Handling actions of plugin "%s" failed'] = array('name' => 'Handling actions of plugin "%s" failed');
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_escape_special_characters.php'), file_get_contents($this->filePath));
	}
	
	public function testWriteStringsToFile_shouldNotKeepTranslationsWithPunctuationOnSuccessiveCalls()
	{
		$obj = $this->getMock('Language_WriteFile', array('getCurrentTranslations', 'fileHeader'));
		$obj->expects($this->at(0))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Errors' => 'Ошибки',
			)
		));
		$obj->expects($this->any())->method('fileHeader')->will($this->returnValue("// File header\n\n"));
		
		$strings = array(
			'Errors' => array('name' => 'Errors'),
			'Errors:' => array('name' => 'Errors:'),
		);
				
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$obj->expects($this->at(0))->method('getCurrentTranslations')->will($this->returnValue(
			array(
				'Errors:' => 'خطاها:',
			)
		));
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_writestringstofile_first_call.php'), file_get_contents($this->filePath));
		
		$obj->writeStringsToFile($strings, $this->filePath);
		
		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_writestringstofile_second_call.php'), file_get_contents($this->filePath));
	}
}

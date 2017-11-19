<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/Exception.php');
require_once('lib/language/WriteFile.php');

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class Language_WriteFileTest extends TikiTestCase
{
	protected $obj;

	protected function setUp()
	{
		// setup a mock filesystem
		$lang = vfsStream::setup('lang');
		$this->langFile = new vfsStreamFile('language.php');
		$lang->addChild($this->langFile);

		$this->parseFile = $this->getMockBuilder('Language_File')
								->setMethods(['getTranslations'])
								->setConstructorArgs([vfsStream::url('lang/language.php')])
								->getMock();

		$this->filePath = vfsStream::url('lang/language.php');

		$this->obj = new Language_WriteFile($this->parseFile);
	}

	public function testConstruct_shouldRaiseExceptionIfFileIsNotWritable()
	{
		$this->langFile->chmod(0444);
		$this->setExpectedException('Language_Exception');
		new Language_WriteFile($this->parseFile);
	}

	public function testWriteStringsToFile_shouldReturnFalseIfEmptyParam()
	{
		$this->assertFalse($this->obj->writeStringsToFile([]));
	}

	public function testWriteStringsToFile_shouldWriteSimpleStrings()
	{
		$this->parseFile->expects($this->once())->method('getTranslations')->will($this->returnValue([]));

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$strings = [
			'First string' => ['name' => 'First string'],
			'Second string' => ['name' => 'Second string'],
			'etc' => ['name' => 'etc'],
		];

		$obj->writeStringsToFile($strings);

		// check if a backup of old language file (in this case an empty file) was created
		$this->assertTrue(file_exists($this->filePath . '.old'));

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_simple.php'), file_get_contents($this->filePath));
	}

	public function writeStringsToFile_provider()
	{
		$strings = [
			'First string' => ['name' => 'First string', 'files' => ['file1', 'file3']],
			'Second string' => ['name' => 'Second string', 'files' => ['file2']],
			'Used string' => ['name' => 'Used string', 'files' => ['file3']],
			'Translation is the same as English string' => ['name' => 'Translation is the same as English string', 'files' => ['file5', 'file1']],
			'etc' => ['name' => 'etc', 'files' => ['file4']],
		];

		return [[$strings]];
	}

	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldKeepTranslationsEvenIfTheyAreEqualToEnglishString($strings)
	{
		$this->parseFile->expects($this->exactly(1))->method('getTranslations')->will(
			$this->returnValue(
				[
					'Unused string' => 'Some translation',
					'Used string' => 'Another translation',
					'Translation is the same as English string' => 'Translation is the same as English string',
				]
			)
		);

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$obj->writeStringsToFile($strings);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}

	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldIgnoreUnusedStrings($strings)
	{
		$this->parseFile->expects($this->exactly(1))->method('getTranslations')->will(
			$this->returnValue(
				[
					'Unused string' => 'Some translation',
					'Used string' => 'Another translation',
					'Translation is the same as English string' => 'Translation is the same as English string',
				]
			)
		);

		$obj = $this->getMockBuilder('Language_WriteFile')
				->setMethods(['fileHeader'])
				->setConstructorArgs([$this->parseFile])
				->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$obj->writeStringsToFile($strings);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations.php'), file_get_contents($this->filePath));
	}

	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldOutputFileWhereStringsWasFound($strings)
	{
		$this->parseFile->expects($this->exactly(1))->method('getTranslations')->will(
			$this->returnValue(
				[
					'Unused string' => 'Some translation',
					'Used string' => 'Another translation',
					'Translation is the same as English string' => 'Translation is the same as English string',
				]
			)
		);

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$obj->writeStringsToFile($strings, true);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_with_translations_and_file_paths.php'), file_get_contents($this->filePath));
	}

	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldConsiderStringsWithPunctuationInEndASpecialCase($strings)
	{
		$this->parseFile->expects($this->exactly(1))->method('getTranslations')->will(
			$this->returnValue(
				[
					'Unused string' => 'Some translation',
					'Used string' => 'Another translation',
					'Translation is the same as English string' => 'Translation is the same as English string',
					'Login' => 'Another translation',
					'Add user:' => 'Translation',
				]
			)
		);

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$strings['Login:'] = ['name' => 'Login:'];
		$strings['Add user:'] = ['name' => 'Add user:'];
		$strings['All users:'] = ['name' => 'All users:'];

		$obj->writeStringsToFile($strings);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_punctuations.php'), file_get_contents($this->filePath));
	}

	/**
	 * @dataProvider writeStringsToFile_provider
	 */
	public function testWriteStringsToFile_shouldProperlyHandleSpecialCharactersInsideStrings($strings)
	{
		$this->parseFile->expects($this->exactly(1))->method('getTranslations')->will(
			$this->returnValue(
				[
					'Unused string' => 'Some translation',
					'Used string' => 'Another translation',
					'Translation is the same as English string' => 'Translation is the same as English string',
					"Congratulations!\n\nYour server can send emails.\n\n" => "Gratulation!\n\nDein Server kann Emails senden.\n\n",
				]
			)
		);

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->once())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$strings["Congratulations!\n\nYour server can send emails.\n\n"] = ['name' => "Congratulations!\n\nYour server can send emails.\n\n"];
		$strings['Handling actions of plugin "%s" failed'] = ['name' => 'Handling actions of plugin "%s" failed'];

		$obj->writeStringsToFile($strings);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_escape_special_characters.php'), file_get_contents($this->filePath));
	}

	public function testWriteStringsToFile_shouldNotKeepTranslationsWithPunctuationOnSuccessiveCalls()
	{
		$this->parseFile->expects($this->at(0))
						->method('getTranslations')->will($this->returnValue(['Errors' => 'Ошибки',]));

		$obj = $this->getMockBuilder('Language_WriteFile')
					->setMethods(['fileHeader'])
					->setConstructorArgs([$this->parseFile])
					->getMock();

		$obj->expects($this->any())->method('fileHeader')->will($this->returnValue("// File header\n\n"));

		$strings = [
			'Errors' => ['name' => 'Errors'],
			'Errors:' => ['name' => 'Errors:'],
		];

		$obj->writeStringsToFile($strings);

		$this->parseFile->expects($this->at(0))
						->method('getTranslations')->will($this->returnValue(['Errors:' => 'خطاها:',]));

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_writestringstofile_first_call.php'), file_get_contents($this->filePath));

		$obj->writeStringsToFile($strings);

		$this->assertEquals(file_get_contents(__DIR__ . '/fixtures/language_writestringstofile_second_call.php'), file_get_contents($this->filePath));
	}
}

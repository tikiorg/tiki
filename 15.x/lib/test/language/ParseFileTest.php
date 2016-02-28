<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/File.php');

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class Language_FileTest extends TikiTestCase
{
	protected $obj;

	protected $filePath;
	
	protected function setUp()
	{
		$this->filePath = __DIR__ . '/fixtures/language_to_parse_file.php';
		$this->obj = new Language_File($this->filePath);
	}

	public function provider()
	{
		return array(array(array(
			'Bytecode Cache' => array('key' => 'Bytecode Cache', 'translated' => false),
			'Used' => array('key' => 'Used', 'translation' => "Usado", 'translated' => true),
		 	'Available' => array('key' => 'Available', 'translated' => false),
			'Memory' => array('key' => 'Memory', 'translated' => false),
			'%0 enabled' => array('key' => '%0 enabled', 'translation' => '%0 habilitado', 'translated' => true),
			'Features' => array('key' => 'Features', 'translation' => 'Recursos', 'translated' => true),
			'Enable/disable Tiki features here, but configure them elsewhere' => array('key' => 'Enable/disable Tiki features here, but configure them elsewhere', 'translated' => false),
		)));
	}
	
	public function testConstruct_shouldThrowExceptionForInvalidFile()
	{
		$this->setExpectedException('Language_Exception', 'Path invalidFile does not exist.');
		$obj = new Language_File('invalidFile');
	}
	
	public function testConstruct_shouldSetFilePath()
	{
		$obj = new Language_File($this->filePath);
		$this->assertEquals($this->filePath, $obj->filePath);
	}
	
    /**
     * @dataProvider provider
     */
	public function testParse_shouldReturnDataStructureRepresentingLanguageFile($expectedResult)
	{
		$this->assertEquals($expectedResult, $this->obj->parse());
	}
	
	public function testParse_shouldSetContentLoadedProperty()
	{
		$reflectionClass = new ReflectionClass($this->obj);
		$property = $reflectionClass->getProperty('contentLoaded');
		$property->setAccessible(true);
		
		$this->obj->parse();
		$this->assertTrue($property->getValue($this->obj));
	}
	
	public function testGetStats_shouldReturnEmptyStats()
	{
		$expectedResult = array(
			'total' => 0,
			'translated' => 0,
			'untranslated' => 0,
			'percentage' =>  0,
		);

		$root = vfsStream::setup('root');
		$file = new vfsStreamFile('language.php');
		$root->addChild($file);
		
		$obj = new Language_File(vfsStream::url('root/language.php'));
		
		$this->assertEquals($expectedResult, $obj->getStats());
	}
	
	public function testGetStats_shouldReturnLangFileStats()
	{
		$expectedResult = array(
			'total' => 7,
			'translated' => 3,
			'untranslated' => 4,
			'percentage' =>  42.86,
		);
		
		$this->assertEquals($expectedResult, $this->obj->getStats());
	}
	
	/**
     * @dataProvider provider
     */
	public function testGetStats_shouldNotCallParseIfContentIsAlreadyLoaded($content)
	{
		$expectedResult = array(
			'total' => 7,
			'translated' => 3,
			'untranslated' => 4,
			'percentage' =>  42.86,
		);
		
		$obj = $this->getMock('Language_File', array('parse'), array($this->filePath));
		$obj->expects($this->never())->method('parse');
		
		$reflectionClass = new ReflectionClass($obj);
		$contentProperty = $reflectionClass->getProperty('content');
		$contentProperty->setAccessible(true);
		$contentProperty->setValue($obj, $content);
		$contentLoadedProperty = $reflectionClass->getProperty('contentLoaded');
		$contentLoadedProperty->setAccessible(true);
		$contentLoadedProperty->setValue($obj, true);
		
		$this->assertEquals($expectedResult, $obj->getStats());
	}
	
	public function testGetTranslations_shouldReturnEmptyArray()
	{
		$root = vfsStream::setup('root');
		$root->addChild(new vfsStreamFile('language.php'));
		$obj = new Language_File(vfsStream::url('root/language.php'));
		$this->assertEquals(array(), $obj->getTranslations());
	}
	
	public function testGetTranslations_shouldReturnTranslations()
	{
		$expectedResult = array(
			"Used" => "Usado",
			"%0 enabled" => "%0 habilitado",
			"Features" => "Recursos",
		);
		
		$this->assertEquals($expectedResult, $this->obj->getTranslations());
	}
}

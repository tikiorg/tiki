<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ParseFileTest.php 37798 2011-09-29 19:59:41Z changi67 $

require_once('lib/language/ParseFile.php');

class Language_ParseFileTest extends TikiTestCase
{
	protected $obj;

	protected $filePath;
	
	protected function setUp()
	{
		$this->filePath = __DIR__ . '/fixtures/language_to_parse_file.php';
		$this->obj = new Language_ParseFile($this->filePath);
	}

	public function testConstruct_shouldThrowExceptionForInvalidFile()
	{
		$this->setExpectedException('Language_Exception', 'Path invalidFile does not exist.');
		$obj = new Language_ParseFile('invalidFile');
	}
	
	public function testConstruct_shouldSetFilePath()
	{
		$obj = new Language_ParseFile($this->filePath);
		$this->assertEquals($this->filePath, $obj->filePath);
	}
	
	public function testParse_shouldReturnDataStructureRepresentingLanguageFile()
	{
		$expectedResult = array(
			'Bytecode Cache' => array('key' => 'Bytecode Cache', 'translated' => false),
			'Used' => array('key' => 'Used', 'translation' => "Usado", 'translated' => true),
		 	'Available' => array('key' => 'Available', 'translated' => false),
			'Memory' => array('key' => 'Memory', 'translated' => false),
			'%0 enabled' => array('key' => '%0 enabled', 'translation' => '%0 habilitado', 'translated' => true),
			'Features' => array('key' => 'Features', 'translation' => 'Recursos', 'translated' => true),
			'Enable/disable Tiki features here, but configure them elsewhere' => array('key' => 'Enable/disable Tiki features here, but configure them elsewhere', 'translated' => false),
		);
		
		$this->assertEquals($expectedResult, $this->obj->parse());
	}
	
}

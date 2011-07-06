<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/language/CollectFiles.php');
require_once('lib/language/WriteFile.php');
require_once('lib/language/GetStrings.php');
require_once('lib/language/FileType.php');
require_once('lib/language/FileType/Php.php');
require_once('lib/language/FileType/Tpl.php');

include_once('vfsStream/vfsStream.php');

class Language_GetStringsEndToEndTest extends TikiTestCase
{	
	protected function setUp()
	{
		if (!class_exists('vfsStream')) {
			$this->markTestSkipped('vfsStream class not available');
		}
		
		// setup a mock filesystem with directories and files 
		$root = vfsStream::setup('root');
		$dir1 = new vfsStreamDirectory('dir1');
		$dir2 = new vfsStreamDirectory('dir2');
		$dir3 = new vfsStreamDirectory('lang');
		$langDir = new vfsStreamDirectory('es');
		
		$file1 = new vfsStreamFile('file1.tpl');
		$file1->setContent(file_get_contents(__DIR__ . '/fixtures/test_collecting_strings.tpl'));
		
		$file2 = new vfsStreamFile('file2.php');
		$file2->setContent(file_get_contents(__DIR__ . '/fixtures/test_collecting_strings.php'));
		
		$langFile = new vfsStreamFile('language.php');
		$langFile->setContent(file_get_contents(__DIR__ . '/fixtures/language_end_to_end_test_original.php'));
		
		$dir1->addChild($file1);
		$dir2->addChild($file2);
		$dir3->addChild($langDir);
		$langDir->addChild($langFile);
		
		$root->addChild($dir1);
		$root->addChild($dir2);
		$root->addChild($dir3);
	}
	
	public function testGetStrings_endToEnd()
	{	
		$obj = new Language_GetStrings(new Language_CollectFiles, new Language_WriteFile, array('baseDir' => vfsStream::url('root')));
		$obj->addFileType(new Language_FileType_Php);
		$obj->addFileType(new Language_FileType_Tpl);
		$obj->run();
		
		$this->assertEquals(
			file_get_contents(__DIR__ . '/fixtures/language_end_to_end_test_modified.php'),
			file_get_contents(vfsStream::url('root/lang/es/language.php'))
		);
	}
}
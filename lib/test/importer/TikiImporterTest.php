<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once(dirname(__FILE__) . '/tikiimporter_testcase.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter.php');
/**
 * @group importer
 */
class TikiImporter_Test extends TikiImporter_TestCase
{
	public function testGetOptions()
	{
		$expectedResult = [['name' => 'name'],
								['name' => 'otherName'],
								['secondName' => 'something']];
		$object = new TikiImporterGranSon();
		$this->assertEquals($expectedResult, $object->getOptions());

		$expectedResult = [['name' => 'someName', 'property1' => 'someProperty'],
								['name' => 'differentName', 'property' => 'anotherProperty']];
		$object = new TikiImporterFirstChild();
		$this->assertEquals($expectedResult, $object->getOptions());
	}

	public function testChangePhpSettings()
	{
		TikiImporter::changePhpSettings();
		$this->assertEquals(E_ALL & ~E_DEPRECATED, ini_get('error_reporting'), 'Should change the value of the error reporting');
		$this->assertEquals('on', ini_get('display_errors'), 'Should change the value of display_errors');
		$this->assertEquals(0, ini_get('max_execution_time'), 'Should change the value of max_execution_time');
	}

	public function testDisplayPhpUploadError()
	{
		$this->assertNull(TikiImporter::displayPhpUploadError(-1), 'Should return null if invalid code passed as param');
		$this->assertEquals('No file was uploaded.', TikiImporter::displayPhpUploadError(4));
	}
}


// dummy classes to test the TikiImporter::getOptions()

class TikiImporterFirstChild extends TikiImporter
{
	public static function importOptions()
	{
		return [
			['name' => 'someName', 'property1' => 'someProperty'],
			['name' => 'differentName', 'property' => 'anotherProperty']
		];
	}
}

class TikiImporterSecondChild extends TikiImporter
{
	public static function importOptions()
	{
		return [
			['name' => 'otherName'],
			['secondName' => 'something']
		];
	}
}

class TikiImporterGranSon extends TikiImporterSecondChild
{
	public static function importOptions()
	{
		 return [
			 ['name' => 'name']
		 ];
	}
}

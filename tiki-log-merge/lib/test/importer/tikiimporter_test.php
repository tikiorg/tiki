<?php

require_once(dirname(__FILE__) . '/tikiimporter_testcase.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter.php');
/** 
 * @group integration
 */
class TikiImporter_Test extends TikiImporter_TestCase
{
    public function testGetOptions()
    {
        $expectedResult = array(array('name' => 'name'),
                                array('name' => 'otherName'),
                                array('secondName' => 'something'));
        $object = new TikiImporterGranSon();
        $this->assertEquals($expectedResult, $object->getOptions());

        $expectedResult = array(array('name' => 'someName', 'property1' => 'someProperty'),
                                array('name' => 'differentName', 'property' => 'anotherProperty'));
        $object = new TikiImporterFirstChild();
        $this->assertEquals($expectedResult, $object->getOptions());
    }

    public function testChangePhpSettings()
    {
        TikiImporter::changePhpSettings();
        $this->assertEquals(E_ALL, ini_get('error_reporting'), 'Should change the value of the error reporting');
        $this->assertEquals(1, ini_get('display_errors'), 'Should change the value of display_errors');
        $this->assertEquals(360, ini_get('max_execution_time'), 'Should change the value of max_execution_time');
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
    static public $importOptions = array(array('name' => 'someName', 'property1' => 'someProperty'),
                                      array('name' => 'differentName', 'property' => 'anotherProperty'));
}

class TikiImporterSecondChild extends TikiImporter
{
    static public $importOptions = array(array('name' => 'otherName'),
                                         array('secondName' => 'something'));
}

class TikiImporterGranSon extends TikiImporterSecondChild
{
    static public $importOptions = array(array('name' => 'name'));
}

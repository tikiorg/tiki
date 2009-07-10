<?php

require_once(dirname(__FILE__) . '/../../core/test/TikiTestCase.php');
require_once(dirname(__FILE__) . '/../tikiimporter.php');

class TikiImporter_Test extends TikiTestCase 
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

?>

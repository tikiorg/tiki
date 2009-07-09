<?php

require_once('PHPUnit/Framework/TestCase.php');
require_once('../tikiimporter_wiki.php');

class TikiImporter_Wiki_Test extends PHPUnit_Framework_TestCase
{
    public function testImportShouldCallMethodsToStartImportProcess()
    {
        $obj = $this->getMock('TikiImporter_Wiki', array('validateInput', 'parseData', 'insertData'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('parseData');
        $obj->expects($this->once())->method('insertData');

        $obj->import();
   }

    public function testImportShouldSetInstanceProperties()
    {
        $obj = $this->getMock('TikiImporter_Wiki', array('validateInput', 'parseData', 'insertData'));
        $_POST['alreadyExistentPageName'] = 'override';
        $_POST['wikiRevisions'] = 100;

        $obj->import();

        $this->assertEquals(100, $obj->revisionsNumber);
        $this->assertEquals('override', $obj->alreadyExistentPageName);

        unset($_POST['alreadyExistentPageName']);
        unset($_POST['wikiRevisions']);
        $obj->import();

        $this->assertEquals(0, $obj->revisionsNumber);
        $this->assertEquals('doNotImport', $obj->alreadyExistentPageName);
    }

    public function testInsertDataCallInsertPageFourTimes()
    {
        $obj = $this->getMock('TikiImporter_Wiki', array('insertPage'));
        $obj->expects($this->exactly(4))->method('insertPage');
        $parsedData = array(1, 2, 3, 4);
        $obj->insertData($parsedData);
    }

    public function testInsertDataCallInsertPageOnceWithProperParam()
    {
        $obj = $this->getMock('TikiImporter_Wiki', array('insertPage'));
        $obj->expects($this->once())->method('insertPage')->with('pageArray');
        $parsedData = array('pageArray');
        $obj->insertData($parsedData);
    }

    public function testInsertDataShouldNotCallInsertPage()
    {
        $obj = $this->getMock('TikiImporter_Wiki', array('insertPage'));
        $obj->expects($this->never())->method('insertPage');
        $parsedData = array();
        $obj->insertData($parsedData);
    }

    public function testInsertPage()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}

?>

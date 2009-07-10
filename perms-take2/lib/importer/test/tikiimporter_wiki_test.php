<?php

require_once('PHPUnit/Framework/TestCase.php');
require_once('PHPUnit/Extensions/Database/TestCase.php');
require_once(dirname(__FILE__) . '/../tikiimporter_wiki.php');

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
        $this->markTestIncomplete('Test not implemented yet');
/*        require_once(dirname(__FILE__) . '/fixtures/mediawiki_page_as_array.php');
        global $tikilib;
        $tikilib = $this->getMock('TikiLib', array('create_page', 'update_page', 'page_exists'));
        $tikilib->expects($this->once())->method('page_exists')->with($page['name']);
        $tikilib->expects($this->once())->method('create_page')->with($page['name'], 0, $page['revisions'][0]['data'], $page['revisions'][0]['lastModif'], $page['revisions'][0]['comment'], $page['revisions'][0]['user'], $page['revisions'][0]['ip']);
        $tikilib->expects($this->once())->method('update_page');
        $first = true;
        foreach ($page['revisions'] as $key => $rev) {
            if (!$first)
                $tikilib->expects($this->once())->method('update_page')->with($page['name'], $rev['data'], $rev['comment'], $rev['user'], $rev['ip'], '', $rev['minor'], '', false, null, $rev['lastModif']);
            $first = false;
        }

        $obj = new TikiImporter_Wiki_Mediawiki;
        // $page is set on mediawiki_page_as_array.php
        $obj->insertPage($page);*/
    }
}

?>

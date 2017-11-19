<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once(dirname(__FILE__) . '/tikiimporter_testcase.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter_wiki.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter_wiki_mediawiki.php');

/**
 * @group importer
 */
class TikiImporter_Wiki_Test extends TikiImporter_TestCase
{

	public function testImportShouldCallMethodsToStartImportProcess()
	{
		ob_start();
		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['validateInput', 'parseData', 'insertData'])
		   ->getMock();
		$obj->expects($this->once())->method('validateInput');
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData');

		$obj->import();

		$output = ob_get_clean();
		$this->assertEquals("\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>", $output);
	}

	public function testImportShouldSetInstanceProperties()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['validateInput', 'parseData', 'insertData'])
		   ->getMock();
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

		ob_get_clean();
	}

	public function testImportShouldSetSessionVariables()
	{
		ob_start();

		$expectedImportFeedback = ['importedPages' => 10, 'totalPages' => '13'];
		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['validateInput', 'parseData', 'insertData', 'saveAndDisplayLog'])
		   ->getMock();
		$obj->expects($this->once())->method('validateInput');
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData')->will($this->returnValue($expectedImportFeedback));
		$obj->expects($this->once())->method('saveAndDisplayLog');

		$obj->log = 'some log string';
		$obj->import();

		$this->assertEquals($expectedImportFeedback, $_SESSION['tiki_importer_feedback']);
		$this->assertEquals('some log string', $_SESSION['tiki_importer_log']);

		ob_get_clean();
	}

	public function testInsertDataCallInsertPageFourTimes()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['insertPage'])
		   ->getMock();
		$obj->expects($this->exactly(4))->method('insertPage');
		$parsedData = [['name' => '1'],['name' => '2'],['name' => '3'],['name' => '4'],];
		$obj->insertData($parsedData);

		ob_get_clean();
	}

	public function testInsertDataCallInsertPageOnceWithProperParam()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['insertPage'])
		   ->getMock();
		$obj->expects($this->once())->method('insertPage')->with(['name' => '1']);
		$parsedData = [['name' => '1'],];
		$obj->insertData($parsedData);

		ob_get_clean();
	}

	public function testInsertDataShouldNotCallInsertPage()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['insertPage'])
		   ->getMock();
		$obj->expects($this->never())->method('insertPage');
		$parsedData = [];
		$obj->insertData($parsedData);

		ob_get_clean();
	}

	public function testInsertDataShouldReturnCountData()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Wiki')
		   ->setMethods(['insertPage'])
		   ->getMock();
		$obj->expects($this->exactly(6))->method('insertPage')->will($this->onConsecutiveCalls(true, true, false, true, false, true));

		$parsedData = [1, 2, 3, 4, 5, 6];
		$countData = $obj->insertData($parsedData);
		$expectedResult = ['totalPages' => 6, 'importedPages' => 4];

		$this->assertEquals($expectedResult, $countData);

		ob_get_clean();
	}
}

class TikiImporter_Wiki_InsertPage_Test extends TikiImporter_TestCase
{

	protected function setUp()
	{
		require_once(dirname(__FILE__) . '/fixtures/mediawiki_page_as_array.php');
		global $tikilib;
		$tikilib = $this->getMockBuilder('TikiLib')
		   ->setMethods(['create_page', 'update_page', 'page_exists', 'remove_all_versions'])
		   ->getMock();
		$this->obj = new TikiImporter_Wiki_Mediawiki;
		$this->obj->revisionsNumber = 0;
	}

	public function testInsertPage()
	{
		global $tikilib, $page;

		$tikilib->expects($this->once())->method('page_exists')->with($page['name'])->will($this->returnValue(false));
		$tikilib->expects($this->once())->method('create_page')->with($page['name'], 0, $page['revisions'][0]['data'], $page['revisions'][0]['lastModif'], $page['revisions'][0]['comment'], $page['revisions'][0]['user'], $page['revisions'][0]['ip']);
	   // TODO: how to test parameters for update_page for the 7 different calls
		$tikilib->expects($this->exactly(7))->method('update_page');

	   // $page is set on mediawiki_page_as_array.php
		$this->assertEquals('Redes de ensino', $this->obj->insertPage($page));
	}

	public function testInsertPageAlreadyExistentPageNameOverride()
	{
		global $tikilib, $page;
		$tikilib->expects($this->once())->method('page_exists')->with($page['name'])->will($this->returnValue(true));
		$tikilib->expects($this->once())->method('remove_all_versions')->with($page['name']);
		$tikilib->expects($this->once())->method('create_page');
		$tikilib->expects($this->exactly(7))->method('update_page');

		$this->obj->alreadyExistentPageName = 'override';
		$this->assertEquals('Redes de ensino', $this->obj->insertPage($page));
	}

	public function testInsertPageAlreadyExistentPageNameAppendPrefix()
	{
		global $tikilib, $page;

		$newPageName = $this->obj->softwareName . '_' . $page['name'];

		$tikilib->expects($this->once())->method('page_exists')->with($page['name'])->will($this->returnValue(true));
		$tikilib->expects($this->once())->method('create_page')->with($newPageName);
		$tikilib->expects($this->exactly(7))->method('update_page')->with($newPageName);

		$this->obj->alreadyExistentPageName = 'appendPrefix';
		$this->assertEquals('Mediawiki_Redes de ensino', $this->obj->insertPage($page));
	}

	public function testInsertPageAlreadyExistentPageNameDoNotImport()
	{
		global $tikilib, $page;

		$tikilib->expects($this->once())->method('page_exists')->with($page['name'])->will($this->returnValue(true));
		$tikilib->expects($this->never())->method('create_page');
		$tikilib->expects($this->never())->method('update_page');

		$this->obj->alreadyExistentPageName = 'doNotImport';
		$this->assertFalse($this->obj->insertPage($page));
	}
}

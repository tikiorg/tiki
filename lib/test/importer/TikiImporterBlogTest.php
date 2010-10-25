<?php

require_once(dirname(__FILE__) . '/tikiimporter_testcase.php');
require_once(dirname(__FILE__) . '/../../importer/tikiimporter_blog.php');

/** 
 * @group importer
 */
class TikiImporter_Blog_Test extends TikiImporter_TestCase
{

	protected function setUp()
	{
		$this->obj = new TikiImporter_Blog();
	}
	
    public function testImportShouldCallMethodsToStartImportProcess()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('validateInput', 'parseData', 'insertData'));
        $obj->expects($this->once())->method('validateInput');
        $obj->expects($this->once())->method('parseData');
        $obj->expects($this->once())->method('insertData');

        $this->expectOutputString("\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>");
        $obj->import();
   }

    public function testImportShouldSetSessionVariables()
    {
        $expectedImportFeedback = array('importedPages' => 10, 'totalPages' => '13');
        $obj = $this->getMock('TikiImporter_Blog', array('validateInput', 'parseData', 'insertData', 'saveAndDisplayLog'));
        $obj->expects($this->once())->method('validateInput'); 
        $obj->expects($this->once())->method('parseData');
        $obj->expects($this->once())->method('insertData')->will($this->returnValue($expectedImportFeedback));
        $obj->expects($this->once())->method('saveAndDisplayLog');
        
        $obj->log = 'some log string';
        $obj->import();

        $this->assertEquals($expectedImportFeedback, $_SESSION['tiki_importer_feedback']);
        $this->assertEquals('some log string', $_SESSION['tiki_importer_log']);
    }

    public function testInsertDataCallInsertPageFourTimes()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertPost', 'createBlog'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(2))->method('insertPage');
        $obj->expects($this->exactly(4))->method('insertPost');
		$parsedData = array(
			array('type' => 'post', 'name' => 'Any name'),
			array('type' => 'post', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'post', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'post', 'name' => 'Any name'),
		);
        $obj->insertData($parsedData);
    }

    public function testInsertDataShouldNotCallInsertPage()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'createBlog'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->never())->method('insertPage');
        $parsedData = array();
        $obj->insertData($parsedData);
    }

    public function testInsertDataShouldReturnCountData()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'createBlog'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(6))->method('insertPage')->will($this->onConsecutiveCalls(true, true, false, true, false, true));

		$parsedData = array(
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name'),
		);

        $countData = $obj->insertData($parsedData);
        $expectedResult = array('totalPages' => 6, 'importedPages' => 4);

        $this->assertEquals($expectedResult, $countData);
	}
	
	public function testInsertDataShouldCallInsertComments()
	{
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'createBlog', 'insertComments'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(6))->method('insertPage')->will($this->onConsecutiveCalls('Any name', 'Any name', false, 'Any name', false, 'Any name'));
        $obj->expects($this->exactly(3))->method('insertComments')->with('Any name', 'wiki page');

		$parsedData = array(
			array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
			array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2)),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name', 'comments' => array()),
			array('type' => 'page', 'name' => 'Any name'),
			array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
		);

        $countData = $obj->insertData($parsedData);
        $expectedResult = array('totalPages' => 6, 'importedPages' => 4);

        $this->assertEquals($expectedResult, $countData);
		
        $obj2 = $this->getMock('TikiImporter_Blog', array('insertPost', 'createBlog', 'insertComments'));
        $obj2->expects($this->once())->method('createBlog');
        $obj2->expects($this->exactly(2))->method('insertPost')->will($this->onConsecutiveCalls('Any name', 'Any name'));
        $obj2->expects($this->exactly(2))->method('insertComments')->with('Any name', 'blog post');

		$parsedData = array(
			array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
			array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2)),
		);

        $countData = $obj2->insertData($parsedData);
        $expectedResult = array('totalPages' => 2, 'importedPages' => 2);

        $this->assertEquals($expectedResult, $countData);
	}

	public function testInsertComments()
	{
		global $commentslib; require_once('lib/comments/commentslib.php');
		
		$commentslib = $this->getMock('Comments', array('post_new_comment'));
		$commentslib->expects($this->exactly(2))->method('post_new_comment')->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '',
			'', 1234, '', '');
		
		$comments = array(
			array('data' => 'asdf', 'created' => 1234),
			array('data' => 'asdf', 'created' => 1234),
		);
		
		$this->obj->insertComments(2, 'wiki page', $comments);
	}
	
	public function testInsertPage()
	{
		$importerWiki = $this->getMock('TikiImporter_Wiki', array('insertPage'));
		$importerWiki->expects($this->once())->method('insertPage');
		$obj = $this->getMock('TikiImporter_Blog', array('instantiateImporterWiki'));
		$obj->expects($this->once())->method('instantiateImporterWiki');

		$obj->importerWiki = $importerWiki;

		$obj->insertPage(array());
	}
}
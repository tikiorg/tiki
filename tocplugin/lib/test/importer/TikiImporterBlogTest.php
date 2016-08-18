<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
		$obj = $this->getMock('TikiImporter_Blog', array('parseData', 'insertData', 'setupTiki'));
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData');
		$obj->expects($this->once())->method('setupTiki');
		
		$this->expectOutputString("\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>");
		$obj->import();
	}

	public function testImportShouldSetSessionVariables()
	{
		$expectedImportFeedback = array('importedPages' => 10, 'totalPages' => '13');
		$obj = $this->getMock('TikiImporter_Blog', array('parseData', 'insertData', 'saveAndDisplayLog', 'setupTiki')); 
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData')->will($this->returnValue($expectedImportFeedback));
		$obj->expects($this->once())->method('saveAndDisplayLog');
		$obj->expects($this->once())->method('setupTiki');
		
		$obj->log = 'some log string';
		$obj->import();
		
		$this->assertEquals($expectedImportFeedback, $_SESSION['tiki_importer_feedback']);
		$this->assertEquals('some log string', $_SESSION['tiki_importer_log']);
	}

    public function testInsertData_shouldCallInsertItemSixTimes()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertItem', 'createBlog'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(6))->method('insertItem');
        
        $obj->permalinks = array('not empty');
        
		$obj->parsedData = array(
			'pages' => array(
				array('type' => 'page', 'name' => 'Any name'),
				array('type' => 'page', 'name' => 'Any name'),
			),
			'posts' => array(
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
			),
			'tags' => array(),
			'categories' => array(),
		);
		
        $obj->insertData();
    }

    public function testInsertData_shouldNotCallInsertItem()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertItem'));
        $obj->expects($this->never())->method('insertItem');
        $obj->parsedData = array(
        	'pages' => array(),
        	'posts' => array(),
        	'tags' => array(),
        	'categories' => array(),
        );
        $obj->insertData();
    }

	public function testInsertData_shouldReturnCountData()
	{
		$obj = $this->getMock('TikiImporter_Blog', array('insertItem', 'createBlog'));
		$obj->expects($this->once())->method('createBlog');
		$obj->expects($this->exactly(6))->method('insertItem')->will($this->onConsecutiveCalls(true, true, true, true, false, true));
		
		$obj->permalinks = array('not empty');

		$obj->parsedData = array(
			'pages' => array(
				array('type' => 'page', 'name' => 'Any name'),
				array('type' => 'page', 'name' => 'Any name'),
			),
			'posts' => array(
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
				array('type' => 'post', 'name' => 'Any name'),
			),
			'tags' => array(),
			'categories' => array(),
		);

        $countData = $obj->insertData();
        $expectedResult = array('importedPages' => 1, 'importedPosts' => 4, 'importedTags' => 0, 'importedCategories' => 0);

        $this->assertEquals($expectedResult, $countData);
	}

	public function testInsertData_shouldNotCreateBlogIfNoPosts()
	{
		$obj = $this->getMock('TikiImporter_Blog', array('insertItem', 'createTags', 'createCategories', 'createBlog'));
        $obj->expects($this->exactly(0))->method('insertItem');
        $obj->expects($this->exactly(0))->method('createTags');
        $obj->expects($this->exactly(0))->method('createCategories');
        $obj->expects($this->exactly(0))->method('createBlog');

		$obj->parsedData = array(
			'pages' => array(),
			'posts' => array(),
			'tags' => array(),
			'categories' => array(),
		);

        $countData = $obj->insertData();
        $expectedResult = array('importedPages' => 0, 'importedPosts' => 0, 'importedTags' => 0, 'importedCategories' => 0);

        $this->assertEquals($expectedResult, $countData);
	}
		
	public function testInsertItem_shouldCallInsertCommentsForPage()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");
        $obj = $this->getMock('TikiImporter_Blog', array('insertComments', 'insertPage'));
        $obj->expects($this->once())->method('insertComments')->with('Any name', 'wiki page');
        $obj->expects($this->once())->method('insertPage')->will($this->onConsecutiveCalls(true));

		$page = array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2, 3));

        $obj->insertItem($page);
	}

	public function testInsertItem_shouldCallInsertCommentsForPost()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");
        $obj = $this->getMock('TikiImporter_Blog', array('insertComments', 'insertPost'));
        $obj->expects($this->once())->method('insertComments')->with('Any name', 'blog post');
        $obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(true));
        
		$post = array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2));

        $obj->insertItem($post);
	}
	
	public function testInsertItem_shouldReturnObjId()
	{
		$obj = $this->getMock('TikiImporter_Blog', array('insertComments', 'insertPost'));
        $obj->expects($this->once())->method('insertComments')->with(22, 'blog post', array(1, 2));
        $obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(22));
        
		$post = array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2));

        $objId = $obj->insertItem($post);
		$this->assertEquals(22, $objId);
	}
	
	public function testInsertItem_shoudReturnNull()
	{
		$obj = $this->getMock('TikiImporter_Blog', array('insertComments', 'insertPost'));
        $obj->expects($this->exactly(0))->method('insertComments');
        $obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(null));
        
		$post = array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2));

        $objId = $obj->insertItem($post);
		$this->assertEquals(null, $objId);
	}
	
	public function testInsertComments()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $commentslib = TikiLib::lib('comments');
		
		$commentslib = $this->getMock('Comments', array('post_new_comment'));
		$commentslib->expects($this->exactly(2))
							->method('post_new_comment')
							->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '', '', 1234, '', '');
		
		$comments = array(
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
		);
		
		$this->obj->insertComments(2, 'wiki page', $comments);
	}
	
	public function testInsertCommentsShouldConsiderIfCommentIsApprovedOrNot()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $commentslib = TikiLib::lib('comments');
		
		$commentslib = $this->getMock('Comments', array('post_new_comment', 'approve_comment'));
		$commentslib->expects($this->exactly(2))
						->method('post_new_comment')
						->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '', '', 1234, '', '')->will($this->returnValue(22));
		$commentslib->expects($this->once())->method('approve_comment')->with(22, 'n');
		
		$comments = array(
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
			array('data' => 'asdf', 'created' => 1234, 'approved' => 0),
		);
		
		$this->obj->insertComments(2, 'wiki page', $comments);
	}
	
	public function testInsertPage()
	{
		$objectlib = TikiLib::lib('object');
		
		$objectlib = $this->getMock('ObjectLib', array('insert_object'));
		$objectlib->expects($this->once())->method('insert_object');
		
		$importerWiki = $this->getMock('TikiImporter_Wiki', array('insertPage'));
		$importerWiki->expects($this->once())->method('insertPage')->will($this->returnValue('HomePage'));
		
		$obj = $this->getMock('TikiImporter_Blog', array('instantiateImporterWiki'));
		$obj->expects($this->once())->method('instantiateImporterWiki');

		$obj->importerWiki = $importerWiki;

		$obj->insertPage(array());
	}
	
	public function testInsertPost()
	{
		$objectlib = TikiLib::lib('object');
		$bloglib = TikiLib::lib('blog');
		
		$bloglib = $this->getMock('BlogLib', array('blog_post'));
		$bloglib->expects($this->once())->method('blog_post')->will($this->returnValue(1));
		
		$objectlib = $this->getMock('ObjectLib', array('insert_object'));
		$objectlib->expects($this->once())->method('insert_object');

		$post = array('content' => 'asdf', 'excerpt' => '', 'author' => 'admin', 'name' => 'blog post title', 'created' => 1234);
		
		$this->obj->insertPost($post);
	}
	
	public function testCreateTags()
	{
		$freetaglib = TikiLib::lib('freetag');
		$freetaglib = $this->getMock('FreetagLib', array('find_or_create_tag'));
		$freetaglib->expects($this->exactly(4))->method('find_or_create_tag');
		
		$tags = array('tag1', 'tag2', 'tag3', 'tag4');
		
		$this->obj->createTags($tags);
	}
	
	public function testCreateCategories()
	{
		$categlib = TikiLib::lib('categ');
		$categlib = $this->getMock('CategLib', array('add_category', 'get_category_id'));
		$categlib->expects($this->exactly(3))->method('add_category');
		$categlib->expects($this->exactly(1))->method('get_category_id');
		
		$categories = array(
			array('parent' => '', 'name' => 'categ1', 'description' => ''),
			array('parent' => '', 'name' => 'categ2', 'description' => ''),
			array('parent' => 'categ1', 'name' => 'categ3', 'description' => ''),
		);
		
		$this->obj->createCategories($categories);
	}

	public function testLinkObjectWithTags()
	{
		$freetaglib = TikiLib::lib('freetag');
		$freetaglib = $this->getMock('FreetagLib', array('_tag_object_array'));
		$freetaglib->expects($this->once())->method('_tag_object_array');
		
		$tags = array('tag1', 'tag2', 'tag3', 'tag4');
		
		$this->obj->linkObjectWithTags('HomePage', 'wiki page', $tags);
	}
	
	public function testLinkObjectWithCategories()
	{
		$categlib = TikiLib::lib('categ');
		$categlib = $this->getMock('CategLib', array('get_category_id', 'get_object_id', 'categorize', 'add_categorized_object'));
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('add_categorized_object');
		
		$categs = array('categ1', 'categ2', 'categ3', 'categ4');
		
		$this->obj->linkObjectWithCategories('HomePage', 'wiki page', $categs);
	}
	
	public function testCreateBlog()
	{
		$bloglib = TikiLib::lib('blog');
		$bloglib = $this->getMock('BlogLib', array('replace_blog'));
		$bloglib->expects($this->once())->method('replace_blog');

		$this->obj->blogInfo = array('title' => 'Test title', 'desc' => 'Test description', 'lastModif' => 12345);
		
		$this->obj->createBlog();
	}
	
	public function testCreateBlogShouldSetBlogAsHomepage()
	{
		$bloglib = TikiLib::lib('blog');
		$bloglib = $this->getMock('BlogLib', array('replace_blog'));
		$bloglib->expects($this->once())->method('replace_blog');
		
		$tikilib = $this->getMock('TikiLib', array('set_preference'));
		$tikilib->expects($this->exactly(2))->method('set_preference');

		$this->obj->blogInfo = array('title' => 'Test title', 'desc' => 'Test description', 'lastModif' => 12345);
		
		$_REQUEST['setAsHomePage'] = 'on';
		
		$this->obj->createBlog();
		
		unset($_REQUEST['setAsHomePage']);
	}
}

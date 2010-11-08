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
        $obj = $this->getMock('TikiImporter_Blog', array('parseData', 'insertData'));
        $obj->expects($this->once())->method('parseData');
        $obj->expects($this->once())->method('insertData');

        $this->expectOutputString("\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>");
        $obj->import();
   }

    public function testImportShouldSetSessionVariables()
    {
        $expectedImportFeedback = array('importedPages' => 10, 'totalPages' => '13');
        $obj = $this->getMock('TikiImporter_Blog', array('parseData', 'insertData', 'saveAndDisplayLog')); 
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
		
        $obj->insertData($parsedData);
    }

    public function testInsertDataShouldNotCallInsertPage()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage'));
        $obj->expects($this->never())->method('insertPage');
        $parsedData = array(
        	'pages' => array(),
        	'posts' => array(),
        	'tags' => array(),
        	'categories' => array(),
        );
        $obj->insertData($parsedData);
    }

    public function testInsertDataShouldReturnCountData()
    {
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertPost', 'createBlog'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(2))->method('insertPage')->will($this->onConsecutiveCalls(true, true));
        $obj->expects($this->exactly(4))->method('insertPost')->will($this->onConsecutiveCalls(true, true, false, true));

		$parsedData = array(
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

        $countData = $obj->insertData($parsedData);
        $expectedResult = array('importedPages' => 2, 'importedPosts' => 3, 'importedTags' => 0, 'importedCategories' => 0);

        $this->assertEquals($expectedResult, $countData);
	}
	
	public function testInsertDataShouldCallInsertComments()
	{
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertComments'));
        $obj->expects($this->exactly(6))->method('insertPage')->will($this->onConsecutiveCalls('Any name', 'Any name', false, 'Any name', false, 'Any name'));
        $obj->expects($this->exactly(3))->method('insertComments')->with('Any name', 'wiki page');

		$parsedData = array(
			'pages' => array(
				array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
				array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2)),
				array('type' => 'page', 'name' => 'Any name'),
				array('type' => 'page', 'name' => 'Any name', 'comments' => array()),
				array('type' => 'page', 'name' => 'Any name'),
				array('type' => 'page', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
			),
			'posts' => array(),
			'tags' => array(),
			'categories' => array(),
		);

        $countData = $obj->insertData($parsedData);
        $expectedResult = array('importedPages' => 4, 'importedPosts' => 0, 'importedTags' => 0, 'importedCategories' => 0);

        $this->assertEquals($expectedResult, $countData);
		
        $obj2 = $this->getMock('TikiImporter_Blog', array('insertPost', 'createBlog', 'insertComments'));
        $obj2->expects($this->once())->method('createBlog');
        $obj2->expects($this->exactly(2))->method('insertPost')->will($this->onConsecutiveCalls('Any name', 'Any name'));
        $obj2->expects($this->exactly(2))->method('insertComments')->with('Any name', 'blog post');

		$parsedData = array(
			'posts' => array(
				array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2, 3)),
				array('type' => 'post', 'name' => 'Any name', 'comments' => array(1, 2)),
			),
			'pages' => array(),
			'tags' => array(),
			'categories' => array(),
		);

        $countData = $obj2->insertData($parsedData);
        $expectedResult = array('importedPages' => 0, 'importedPosts' => 2, 'importedTags' => 0, 'importedCategories' => 0);

        $this->assertEquals($expectedResult, $countData);
	}

	public function testInsertDataShouldNotCreateBlogIfNoPosts()
	{
		$obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertComments', 'createTags', 'createCategories', 'createBlog'));
        $obj->expects($this->exactly(0))->method('insertPage');
        $obj->expects($this->exactly(0))->method('insertComments');
        $obj->expects($this->exactly(0))->method('createTags');
        $obj->expects($this->exactly(0))->method('createCategories');
        $obj->expects($this->exactly(0))->method('createBlog');

		$parsedData = array(
			'pages' => array(),
			'posts' => array(),
			'tags' => array(),
			'categories' => array(),
		);

        $countData = $obj->insertData($parsedData);
        $expectedResult = array('importedPages' => 0, 'importedPosts' => 0, 'importedTags' => 0, 'importedCategories' => 0);

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
		global $objectlib; require_once('lib/objectlib.php');
		
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
		global $objectlib; require_once('lib/objectlib.php');
		global $bloglib; require_once('lib/blogs/bloglib.php');
		
		$bloglib = $this->getMock('BlogLib', array('blog_post'));
		$bloglib->expects($this->once())->method('blog_post')->will($this->returnValue(1));
		
		$objectlib = $this->getMock('ObjectLib', array('insert_object'));
		$objectlib->expects($this->once())->method('insert_object');

		$post = array('content' => 'asdf', 'excerpt' => '', 'author' => 'admin', 'name' => 'blog post title', 'created' => 1234);
		
		$this->obj->insertPost($post);
	}
	
	public function testCreateTags()
	{
		global $freetaglib; require_once('lib/freetag/freetaglib.php');
		$freetaglib = $this->getMock('FreetagLib', array('find_or_create_tag'));
		$freetaglib->expects($this->exactly(4))->method('find_or_create_tag');
		
		$tags = array('tag1', 'tag2', 'tag3', 'tag4');
		
		$this->obj->createTags($tags);
	}
	
	public function testCreateCategories()
	{
		global $categlib; require_once('lib/categories/categlib.php');
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
		global $freetaglib; require_once('lib/freetag/freetaglib.php');
		$freetaglib = $this->getMock('FreetagLib', array('_tag_object_array'));
		$freetaglib->expects($this->once())->method('_tag_object_array');
		
		$tags = array('tag1', 'tag2', 'tag3', 'tag4');
		
		$this->obj->linkObjectWithTags('HomePage', 'wiki page', $tags);
	}
	
	public function testLinkObjectWithCategories()
	{
		global $categlib; require_once('lib/categories/categlib.php');
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
		global $bloglib;
		
		$bloglib = $this->getMock('BlogLib', array('replace_blog'));
		$bloglib->expects($this->once())->method('replace_blog');

		$this->obj->blogInfo = array('title' => 'Test title', 'desc' => 'Test description', 'lastModif' => 12345);
		
		$this->obj->createBlog();
	}
	
	public function testCreateBlogShouldSetBlogAsHomepage()
	{
		global $bloglib, $tikilib;
		
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
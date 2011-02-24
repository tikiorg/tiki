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
	
	protected function tearDown()
	{
		TikiDb::get()->query('DELETE FROM tiki_pages WHERE pageName = "materia"');
		TikiDb::get()->query('DELETE FROM tiki_blog_posts WHERE postId = 10');
		unset($GLOBALS['prefs']['feature_sefurl']);
		unset($GLOBALS['base_url']);
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
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertPost', 'createBlog', 'replaceInternalLinks'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(2))->method('insertPage');
        $obj->expects($this->exactly(4))->method('insertPost');
        $obj->expects($this->once())->method('replaceInternalLinks');
        
        $obj->permalinks = array('not empty');
        
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
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertPost', 'createBlog', 'storeNewLink', 'replaceInternalLinks'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->exactly(2))->method('insertPage')->will($this->onConsecutiveCalls(true, true));
        $obj->expects($this->exactly(4))->method('insertPost')->will($this->onConsecutiveCalls(true, true, false, true));
        $obj->expects($this->exactly(5))->method('storeNewLink');
        $obj->expects($this->once())->method('replaceInternalLinks');

        $obj->permalinks = array('not empty');
        
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
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertComments', 'storeNewLink', 'replaceInternalLinks'));
        $obj->expects($this->exactly(6))->method('insertPage')->will($this->onConsecutiveCalls('Any name', 'Any name', false, 'Any name', false, 'Any name'));
        $obj->expects($this->exactly(3))->method('insertComments')->with('Any name', 'wiki page');
        $obj->expects($this->exactly(4))->method('storeNewLink');
        $obj->expects($this->once())->method('replaceInternalLinks');

        $obj->permalinks = array('not empty');
        
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
		
        $obj2 = $this->getMock('TikiImporter_Blog', array('insertPost', 'createBlog', 'insertComments', 'storeNewLink', 'replaceInternalLinks'));
        $obj2->expects($this->once())->method('createBlog');
        $obj2->expects($this->exactly(2))->method('insertPost')->will($this->onConsecutiveCalls('Any name', 'Any name'));
        $obj2->expects($this->exactly(2))->method('insertComments')->with('Any name', 'blog post');
        $obj2->expects($this->exactly(2))->method('storeNewLink');
        $obj2->expects($this->once())->method('replaceInternalLinks');

        $obj2->permalinks = array('not empty');
        
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
		$obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertComments', 'createTags', 'createCategories', 'createBlog', 'storeNewLink'));
        $obj->expects($this->exactly(0))->method('insertPage');
        $obj->expects($this->exactly(0))->method('insertComments');
        $obj->expects($this->exactly(0))->method('createTags');
        $obj->expects($this->exactly(0))->method('createCategories');
        $obj->expects($this->exactly(0))->method('createBlog');
        $obj->expects($this->exactly(0))->method('storeNewLink');

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
	
	public function testInsertDataShouldSetObjIdOnItemsArray()
	{
        $obj = $this->getMock('TikiImporter_Blog', array('insertPage', 'insertPost', 'createBlog', 'storeNewLink', 'replaceInternalLinks'));
        $obj->expects($this->once())->method('createBlog');
        $obj->expects($this->once())->method('insertPage')->will($this->onConsecutiveCalls('Name 1'));
        $obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(2));
        $obj->expects($this->exactly(2))->method('storeNewLink');

        $obj->permalinks = array('not empty');
        
		$parsedData = array(
			'pages' => array(
				array('type' => 'page', 'name' => 'Any name'),
			),
			'posts' => array(
				array('type' => 'post', 'name' => 'Any name'),
			),
			'tags' => array(),
			'categories' => array(),
		);

		$expectedResult = array(
			array('type' => 'post', 'name' => 'Any name', 'objId' => 2),
			array('type' => 'page', 'name' => 'Any name', 'objId' => 'Name 1'),
		);
		$obj->expects($this->once())->method('replaceInternalLinks')->with($expectedResult);
		
        $countData = $obj->insertData($parsedData);
	}
	
	public function testInsertComments()
	{
		global $commentslib; require_once('lib/comments/commentslib.php');
		
		$commentslib = $this->getMock('Comments', array('post_new_comment'));
		$commentslib->expects($this->exactly(2))->method('post_new_comment')->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '',
			'', 1234, '', '');
		
		$comments = array(
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
		);
		
		$this->obj->insertComments(2, 'wiki page', $comments);
	}
	
	public function testInsertCommentsShouldConsiderIfCommentIsApprovedOrNot()
	{
		global $commentslib; require_once('lib/comments/commentslib.php');
		
		$commentslib = $this->getMock('Comments', array('post_new_comment', 'approve_comment'));
		$commentslib->expects($this->exactly(2))->method('post_new_comment')->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '',
			'', 1234, '', '')->will($this->returnValue(22));
		$commentslib->expects($this->once())->method('approve_comment')->with(22, 'n');
		
		$comments = array(
			array('data' => 'asdf', 'created' => 1234, 'approved' => 1),
			array('data' => 'asdf', 'created' => 1234, 'approved' => 0),
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
	
	public function testStoreNewLinkWithSefUrlEnabled()
	{
		global $prefs, $base_url;
		$prefs['feature_sefurl'] = 'y';
		$base_url = 'http://localhost/tiki';
		
		$this->obj->permalinks = array(
			107 => array(
				'oldLinks' => array(
					'http://example.com/materia/',
					'http://example.com/?p=107',
				),
			),
			36 => array(
				'oldLinks' => array(
					'http://example.com/2008/01/20/circuito-grande-torres-del-paine/',
					'http://example.com/?p=36',
				),
			),
		);
		
		$expectedResult = $this->obj->permalinks;
		$expectedResult[107]['newLink'] = 'http://localhost/tiki/materia';
		$expectedResult[36]['newLink'] =  'http://localhost/tiki/blogpost10';
		
		$this->obj->storeNewLink('materia', array('wp_id' => 107, 'type' => 'page'));
		$this->obj->storeNewLink(10, array('wp_id' => 36, 'type' => 'post'));
		
		$this->assertEquals($expectedResult, $this->obj->permalinks);
	}
	
	public function testStoreNewLinkWithSefUrlDisabled()
	{
		global $prefs, $base_url;
		$prefs['feature_sefurl'] = 'n';
		$base_url = 'http://localhost/tiki';
		
		$this->obj->permalinks = array(
			107 => array(
				'oldLinks' => array(
					'http://example.com/materia/',
					'http://example.com/?p=107',
				),
			),
			36 => array(
				'oldLinks' => array(
					'http://example.com/2008/01/20/circuito-grande-torres-del-paine/',
					'http://example.com/?p=36',
				),
			),
		);
		
		$expectedResult = $this->obj->permalinks;
		$expectedResult[107]['newLink'] = 'http://localhost/tiki/tiki-index.php?page=materia';
		$expectedResult[36]['newLink'] =  'http://localhost/tiki/tiki-view_blog_post.php?postId=10';
		
		$this->obj->storeNewLink('materia', array('wp_id' => 107, 'type' => 'page'));
		$this->obj->storeNewLink(10, array('wp_id' => 36, 'type' => 'post'));
		
		$this->assertEquals($expectedResult, $this->obj->permalinks);
	}
	
	public function testReplaceInternalLinks()
	{
		$this->obj->permalinks = array(
			36 => array(
				'oldLinks' => array(
					'http://example.com/2008/01/20/circuito-grande-torres-del-paine/',
					'http://example.com/?p=36',
				),
				'newLink' => 'http://localhost/tiki/tiki-view_blog_post.php?postId=10',
			),
		);

		$items = array(
			array('type' => 'page', 'name' => 'materia', 'hasInternalLinks' => true, 'objId' => 'materia'),
			array('type' => 'post', 'name' => 'Any name', 'hasInternalLinks' => true, 'objId' => 10),
			array('type' => 'post', 'name' => 'Any name', 'hasInternalLinks' => false, 'objId' => 11),
		);
		
		$content = file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_internal_links.txt');
		
		TikiDb::get()->query('INSERT INTO tiki_pages (pageName, data) VALUES (?, ?)',
			array('materia', $content));
		TikiDb::get()->query('INSERT INTO tiki_blog_posts (postId, data) VALUES (?, ?)',
			array(10, $content));
		
		$this->obj->replaceInternalLinks($items);
        
		$newPageContent = TikiDb::get()->getOne('SELECT data FROM tiki_pages WHERE pageName = "materia"');
		$newPostContent = TikiDb::get()->getOne('SELECT data FROM tiki_blog_posts WHERE postId = 10');
		
		$this->assertEquals(file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_internal_links_replaced.txt'),
			$newPageContent);
		$this->assertEquals(file_get_contents(dirname(__FILE__) . '/fixtures/wordpress_post_content_internal_links_replaced.txt'),
			$newPostContent);
	}
}
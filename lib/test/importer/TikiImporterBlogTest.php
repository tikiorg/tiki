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
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['parseData', 'insertData', 'setupTiki'])
			->getMock();
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData');
		$obj->expects($this->once())->method('setupTiki');

		$obj->import();

		$output = ob_get_clean();
		$this->assertEquals("\nImportation completed!\n\n<b><a href=\"tiki-importer.php\">Click here</a> to finish the import process</b>", $output);
	}

	public function testImportShouldSetSessionVariables()
	{
		ob_start();

		$expectedImportFeedback = ['importedPages' => 10, 'totalPages' => '13'];
		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['parseData', 'insertData', 'saveAndDisplayLog', 'setupTiki'])
			->getMock();
		$obj->expects($this->once())->method('parseData');
		$obj->expects($this->once())->method('insertData')->will($this->returnValue($expectedImportFeedback));
		$obj->expects($this->once())->method('saveAndDisplayLog');
		$obj->expects($this->once())->method('setupTiki');

		$obj->log = 'some log string';
		$obj->import();

		$this->assertEquals($expectedImportFeedback, $_SESSION['tiki_importer_feedback']);
		$this->assertEquals('some log string', $_SESSION['tiki_importer_log']);

		ob_get_clean();
	}

	public function testInsertData_shouldCallInsertItemSixTimes()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertItem', 'createBlog'])
			->getMock();
		$obj->expects($this->once())->method('createBlog');
		$obj->expects($this->exactly(6))->method('insertItem');

				$obj->permalinks = ['not empty'];

				$obj->parsedData = [
			'pages' => [
				['type' => 'page', 'name' => 'Any name'],
				['type' => 'page', 'name' => 'Any name'],
			],
			'posts' => [
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
			],
			'tags' => [],
			'categories' => [],
				];

				$obj->insertData();

				ob_get_clean();
	}

	public function testInsertData_shouldNotCallInsertItem()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertItem'])
			->getMock();
		$obj->expects($this->never())->method('insertItem');
		$obj->parsedData = [
			'pages' => [],
			'posts' => [],
			'tags' => [],
			'categories' => [],
		];
		$obj->insertData();

		ob_get_clean();
	}

	public function testInsertData_shouldReturnCountData()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertItem', 'createBlog'])
			->getMock();
		$obj->expects($this->once())->method('createBlog');
		$obj->expects($this->exactly(6))->method('insertItem')->will($this->onConsecutiveCalls(true, true, true, true, false, true));

		$obj->permalinks = ['not empty'];

		$obj->parsedData = [
			'pages' => [
				['type' => 'page', 'name' => 'Any name'],
				['type' => 'page', 'name' => 'Any name'],
			],
			'posts' => [
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
				['type' => 'post', 'name' => 'Any name'],
			],
			'tags' => [],
			'categories' => [],
		];

		$countData = $obj->insertData();
		$expectedResult = ['importedPages' => 1, 'importedPosts' => 4, 'importedTags' => 0, 'importedCategories' => 0];

		$this->assertEquals($expectedResult, $countData);

		ob_get_clean();
	}

	public function testInsertData_shouldNotCreateBlogIfNoPosts()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertItem', 'createTags', 'createCategories', 'createBlog'])
			->getMock();
		$obj->expects($this->exactly(0))->method('insertItem');
		$obj->expects($this->exactly(0))->method('createTags');
		$obj->expects($this->exactly(0))->method('createCategories');
		$obj->expects($this->exactly(0))->method('createBlog');

		$obj->parsedData = [
			'pages' => [],
			'posts' => [],
			'tags' => [],
			'categories' => [],
		];

		$countData = $obj->insertData();
		$expectedResult = ['importedPages' => 0, 'importedPosts' => 0, 'importedTags' => 0, 'importedCategories' => 0];

		$this->assertEquals($expectedResult, $countData);

		ob_get_clean();
	}

	public function testInsertItem_shouldCallInsertCommentsForPage()
	{
		$this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");
		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertComments', 'insertPage'])
			->getMock();
		$obj->expects($this->once())->method('insertComments')->with('Any name', 'wiki page');
		$obj->expects($this->once())->method('insertPage')->will($this->onConsecutiveCalls(true));

		$page = ['type' => 'page', 'name' => 'Any name', 'comments' => [1, 2, 3]];

		$obj->insertItem($page);
	}

	public function testInsertItem_shouldCallInsertCommentsForPost()
	{
		$this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");
		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertComments', 'insertPost'])
			->getMock();
		$obj->expects($this->once())->method('insertComments')->with('Any name', 'blog post');
		$obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(true));

				$post = ['type' => 'post', 'name' => 'Any name', 'comments' => [1, 2]];

		$obj->insertItem($post);
	}

	public function testInsertItem_shouldReturnObjId()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertComments', 'insertPost'])
			->getMock();
		$obj->expects($this->once())->method('insertComments')->with(22, 'blog post', [1, 2]);
		$obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(22));

				$post = ['type' => 'post', 'name' => 'Any name', 'comments' => [1, 2]];

		$objId = $obj->insertItem($post);
		$this->assertEquals(22, $objId);

		ob_get_clean();
	}

	public function testInsertItem_shoudReturnNull()
	{
		ob_start();

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['insertComments', 'insertPost'])
			->getMock();
		$obj->expects($this->exactly(0))->method('insertComments');
		$obj->expects($this->once())->method('insertPost')->will($this->onConsecutiveCalls(null));

				$post = ['type' => 'post', 'name' => 'Any name', 'comments' => [1, 2]];

		$objId = $obj->insertItem($post);
		$this->assertEquals(null, $objId);

		ob_get_clean();
	}

	public function testInsertComments()
	{
		$this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

		$commentslib = TikiLib::lib('comments');

		$commentslib = $this->getMockBuilder('Comments')
			->setMethods(['post_new_comment'])
			->getMock();
		$commentslib->expects($this->exactly(2))
							->method('post_new_comment')
							->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '', '', 1234, '', '');

		$comments = [
			['data' => 'asdf', 'created' => 1234, 'approved' => 1],
			['data' => 'asdf', 'created' => 1234, 'approved' => 1],
		];

		$this->obj->insertComments(2, 'wiki page', $comments);
	}

	public function testInsertCommentsShouldConsiderIfCommentIsApprovedOrNot()
	{
		$this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

		$commentslib = TikiLib::lib('comments');

		$commentslib = $this->getMockBuilder('Comments')
			->setMethods(['post_new_comment', 'approve_comment'])
			->getMock();
		$commentslib->expects($this->exactly(2))
						->method('post_new_comment')
						->with('wiki page:2', 0, null, '', 'asdf', '', '', 'n', '', '', '', '', 1234, '', '')->will($this->returnValue(22));
		$commentslib->expects($this->once())->method('approve_comment')->with(22, 'n');

		$comments = [
			['data' => 'asdf', 'created' => 1234, 'approved' => 1],
			['data' => 'asdf', 'created' => 1234, 'approved' => 0],
		];

		$this->obj->insertComments(2, 'wiki page', $comments);
	}

	public function testInsertPage()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$objectlib = TikiLib::lib('object');

		$objectlib = $this->getMockBuilder('ObjectLib')
			->setMethods(['insert_object'])
			->getMock();
		$objectlib->expects($this->once())->method('insert_object');

		$importerWiki = $this->getMockBuilder('TikiImporter_Wiki')
			->setMethods(['insertPage'])
			->getMock();
		$importerWiki->expects($this->once())->method('insertPage')->will($this->returnValue('HomePage'));

		$obj = $this->getMockBuilder('TikiImporter_Blog')
			->setMethods(['instantiateImporterWiki'])
			->getMock();
		$obj->expects($this->once())->method('instantiateImporterWiki');

		$obj->importerWiki = $importerWiki;

		$obj->insertPage([]);
	}

	public function testInsertPost()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$objectlib = TikiLib::lib('object');
		$bloglib = TikiLib::lib('blog');

		$bloglib = $this->getMockBuilder('BlogLib')
			->setMethods(['blog_post'])
			->getMock();
		$bloglib->expects($this->once())->method('blog_post')->will($this->returnValue(1));

		$objectlib = $this->getMockBuilder('ObjectLib')
			->setMethods(['insert_object'])
			->getMock();
		$objectlib->expects($this->once())->method('insert_object');

		$post = ['content' => 'asdf', 'excerpt' => '', 'author' => 'admin', 'name' => 'blog post title', 'created' => 1234];

		$this->obj->insertPost($post);
	}

	public function testCreateTags()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$freetaglib = TikiLib::lib('freetag');
		$freetaglib = $this->getMockBuilder('FreetagLib')
			->setMethods(['find_or_create_tag'])
			->getMock();
		$freetaglib->expects($this->exactly(4))->method('find_or_create_tag');

		$tags = ['tag1', 'tag2', 'tag3', 'tag4'];

		$this->obj->createTags($tags);
	}

	public function testCreateCategories()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$categlib = TikiLib::lib('categ');
		$categlib = $this->getMockBuilder('CategLib')
			->setMethods(['add_category', 'get_category_id'])
			->getMock();
		$categlib->expects($this->exactly(3))->method('add_category');
		$categlib->expects($this->exactly(1))->method('get_category_id');

		$categories = [
			['parent' => '', 'name' => 'categ1', 'description' => ''],
			['parent' => '', 'name' => 'categ2', 'description' => ''],
			['parent' => 'categ1', 'name' => 'categ3', 'description' => ''],
		];

		$this->obj->createCategories($categories);
	}

	public function testLinkObjectWithTags()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$freetaglib = TikiLib::lib('freetag');
		$freetaglib = $this->getMockBuilder('FreetagLib')
			->setMethods(['_tag_object_array'])
			->getMock();
		$freetaglib->expects($this->once())->method('_tag_object_array');

		$tags = ['tag1', 'tag2', 'tag3', 'tag4'];

		$this->obj->linkObjectWithTags('HomePage', 'wiki page', $tags);
	}

	public function testLinkObjectWithCategories()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$categlib = TikiLib::lib('categ');
		$categlib = $this->getMockBuilder('CategLib')
			->setMethods(['get_category_id', 'get_object_id', 'categorize', 'add_categorized_object'])
			->getMock();
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('get_category_id');
		$categlib->expects($this->exactly(4))->method('add_categorized_object');

		$categs = ['categ1', 'categ2', 'categ3', 'categ4'];

		$this->obj->linkObjectWithCategories('HomePage', 'wiki page', $categs);
	}

	public function testCreateBlog()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$bloglib = TikiLib::lib('blog');
		$bloglib = $this->getMockBuilder('BlogLib')
			->setMethods(['replace_blog'])
			->getMock();
		$bloglib->expects($this->once())->method('replace_blog');

		$this->obj->blogInfo = ['title' => 'Test title', 'desc' => 'Test description', 'lastModif' => 12345];

		$this->obj->createBlog();
	}

	public function testCreateBlogShouldSetBlogAsHomepage()
	{
		$this->markTestSkipped('2016-09-26 Skipped as dependency injection has stopped mock objects working like this.');

		$bloglib = TikiLib::lib('blog');
		$bloglib = $this->getMockBuilder('BlogLib')
			->setMethods(['replace_blog'])
			->getMock();
		$bloglib->expects($this->once())->method('replace_blog');

		$tikilib = $this->getMockBuilder('TikiLib')
			->setMethods(['set_preference'])
			->getMock();
		$tikilib->expects($this->exactly(2))->method('set_preference');

		$this->obj->blogInfo = ['title' => 'Test title', 'desc' => 'Test description', 'lastModif' => 12345];

		$_REQUEST['setAsHomePage'] = 'on';

		$this->obj->createBlog();

		unset($_REQUEST['setAsHomePage']);
	}
}

<?php

/**
 * @group unit
 */
class Search_Index_LuceneTest extends PHPUnit_Framework_TestCase
{
	private $dir;
	private $index;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir);
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(array(
			'object_type' => $typeFactory->identifier('wiki page'),
			'object_id' => $typeFactory->identifier('HomePage'),
			'description' => $typeFactory->plaintext('a description for the page'),
			'wiki_content' => $typeFactory->wikitext('Hello world!'),
			'categories' => $typeFactory->multivalue(array(1, 2, 5, 6)),
		));

		$this->index = $index;
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function testBasicSearch()
	{
		$positive = new Search_Query('Hello');
		$negative = new Search_Query('NotInDocument');

		$this->assertContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $positive->search($this->index));
		$this->assertNotContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $negative->search($this->index));
	}
	
	function testFieldSpecificSearch()
	{
		$off = new Search_Query;
		$off->addTextCriteria('description', 'wiki_content');
		$found = new Search_Query;
		$found->addTextCriteria('description', 'description');

		$this->assertGreaterThan(0, count($found->search($this->index)));
		$this->assertEquals(0, count($off->search($this->index)));
	}

	function testWithOrCondition()
	{
		$query = new Search_Query('foobar or hello');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testWithNotCondition()
	{
		$query = new Search_Query('not world and hello');
		$result = $query->search($this->index);

		$this->assertEquals(0, count($result));
	}

	function testFilterType()
	{
		$this->assertResultCount(0, 'filterType', 'wiki');
		$this->assertResultCount(0, 'filterType', 'blog post');
		$this->assertResultCount(1, 'filterType', 'wiki page');
	}

	function testFilterCategories()
	{
		$this->assertResultCount(1, 'filterCategory', '1 and 2');
		$this->assertResultCount(0, 'filterCategory', '1 and not 2');
		$this->assertResultCount(1, 'filterCategory', '1 and (2 or 3)');
	}

	private function assertResultCount($count, $filterMethod, $argument)
	{
		$query = new Search_Query;
		$query->$filterMethod($argument);

		$this->assertEquals($count, count($query->search($this->index)));
	}
}


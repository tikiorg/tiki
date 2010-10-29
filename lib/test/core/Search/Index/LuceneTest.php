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
		$correct = new Search_Query;
		$correct->filterType('wiki page');

		$invalidType = new Search_Query;
		$invalidType->filterType('wiki');

		$noResult = new Search_Query;
		$noResult->filterType('blog post');

		$this->assertEquals(0, count($invalidType->search($this->index)));
		$this->assertEquals(0, count($noResult->search($this->index)));
		$this->assertGreaterThan(0, count($correct->search($this->index)));
	}
}


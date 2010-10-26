<?php

/**
 * @group unit
 */
class Search_Index_LuceneTest extends PHPUnit_Framework_TestCase
{
	private $dir;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function testWriteIndex()
	{
		$index = new Search_Index_Lucene($this->dir);
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(array(
			'object_type' => $typeFactory->identifier('wiki page'),
			'object_id' => $typeFactory->identifier('HomePage'),
			'wiki_content' => $typeFactory->wikitext('Hello world!'),
		));

		$this->assertContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $index->rawQuery('+Hello'));
		$this->assertNotContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $index->rawQuery('+NotInDocument'));
	}
}


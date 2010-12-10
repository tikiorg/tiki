<?php

/**
 * @group unit
 */
class Search_Index_LuceneStemmingTest extends PHPUnit_Framework_TestCase
{
	private $dir;
	private $index;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir, 'en');
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(array(
			'object_type' => $typeFactory->identifier('wiki page'),
			'object_id' => $typeFactory->identifier('HomePage'),
			'description' => $typeFactory->plaintext('a description for the pages éducation Case'),
		));

		$this->index = $index;
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function testSearchWithAdditionalS()
	{
		$query = new Search_Query('descriptions');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testSearchWithMissingS()
	{
		$query = new Search_Query('page');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testSearchAccents()
	{
		$query = new Search_Query('education');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testSearchExtraAccents()
	{
		$query = new Search_Query('pagé');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testCaseSensitivity()
	{
		$query = new Search_Query('casE');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}
}


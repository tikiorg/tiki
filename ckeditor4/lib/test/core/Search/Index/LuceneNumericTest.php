<?php

class Search_Index_LuceneNumericTest extends PHPUnit_Framework_TestCase
{
	protected $index;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir);

		$this->populate($index);
		$this->index = $index;
	}

	protected function populate($index)
	{
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier('wiki page'),
				'object_id' => $typeFactory->identifier('HomePage'),
				'contents' => $typeFactory->plaintext('module 7, 2.5.3')->filter(
					array(
						new Search_ContentFilter_VersionNumber,
					)
				),
			)
		);
	}

	function tearDown()
	{
		$this->index->destroy();
	}

	function testMatchVersion()
	{
		$this->assertResultCount(1, '2.5.3');
	}

	function testNoMatchLesserVersionPortion()
	{
		$this->assertResultCount(0, '5.3');
	}

	function testMatchHigherVersionPortion()
	{
		$this->assertResultCount(1, '2.5');
	}

	private function assertResultCount($count, $argument)
	{
		$query = new Search_Query;
		$query->filterContent($argument);

		$this->assertEquals($count, count($query->search($this->index)));
	}
}

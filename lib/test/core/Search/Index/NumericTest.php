<?php

abstract class Search_Index_NumericTest extends PHPUnit_Framework_TestCase
{
	protected $index;

	protected function populate($index)
	{
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(
			[
				'object_type' => $typeFactory->identifier('wiki page'),
				'object_id' => $typeFactory->identifier('HomePage'),
				'contents' => $typeFactory->plaintext('module 7, 2.5.3')->filter(
					[
						new Search_ContentFilter_VersionNumber,
					]
				),
			]
		);
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

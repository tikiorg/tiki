<?php

class Search_Elastic_MoreLikeThisTest extends PHPUnit_Framework_TestCase
{
	private $index;
	private $indexer;

	function setUp()
	{
		$connection = new Search_Elastic_Connection('http://localhost:9200');
		$connection->startBulk();

		$status = $connection->getStatus();
		if (! $status->ok) {
			$this->markTestSkipped('ElasticSearch needs to be available on localhost:9200 for the test to run.');
		}

		$this->index = new Search_Elastic_Index($connection, 'test_index');
		$this->index->destroy();

		$this->populate($this->index);
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}

	function populate($index)
	{
		$data = array(
			'X' => array(
				'wiki_content' => 'this does not work',
			),
		);

		$words = array('hello', 'world', 'some', 'random', 'content', 'populated', 'through', 'automatic', 'sampling');

		// Generate 50 documents with random words (in a stable way)
		foreach (range(1, 50) as $doc) {
			$parts = array();
			foreach ($words as $key => $word) {
				if ($doc % ($key + 2) === 0) {
					$parts[] = $word;
					$parts[] = $word;
					$parts[] = $word;
				}
			}

			$data[$doc] = array(
				'object_type' => 'wiki page',
				'object_id' => $doc,
				'wiki_content' => implode(' ', $parts),
			);
		}

		$source = new Search_ContentSource_Static(
			$data, array(
				'object_type' => 'identifier',
				'object_id' => 'identifier',
				'wiki_content' => 'plaintext',
			)
		);

		$this->indexer = new Search_Indexer($index);
		$this->indexer->addContentSource('wiki page', $source);

		$this->indexer->rebuild();
	}

	function testObtainSimilarDocument()
	{
		$query = new Search_Query;
		$query->filterSimilar('wiki page', 12);

		$results = $query->search($this->index);

		$this->assertGreaterThan(0, count($results));
	}

	function testDocumentTooDifferent()
	{
		$query = new Search_Query;
		$query->filterSimilar('wiki page', 'X');

		$results = $query->search($this->index);

		$this->assertCount(0, $results);
	}
}


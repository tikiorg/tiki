<?php

class Search_Elastic_FederatedQueryTest extends PHPUnit_Framework_TestCase
{
	private $indexA;
	private $indexB;

	function setUp()
	{
		$connection = new Search_Elastic_Connection('http://localhost:9200');

		$status = $connection->getStatus();
		if (! $status->ok) {
			$this->markTestSkipped('ElasticSearch needs to be available on localhost:9200 for the test to run.');
		}

		$this->indexA = new Search_Elastic_Index($connection, 'test_index_a');
		$this->indexA->destroy();
		$factory = $this->indexA->getTypeFactory();
		$this->indexA->addDocument(array(
			'object_type' => $factory->identifier('wiki page'),
			'object_id' => $factory->identifier('PageA'),
			'contents' => $factory->plaintext('Hello World A'),
			'url' => $factory->identifier('PageA'),
		));

		$this->indexB = new Search_Elastic_Index($connection, 'test_index_b_foo');
		$this->indexB->destroy();
		$factory = $this->indexB->getTypeFactory();
		$this->indexB->addDocument(array(
			'object_type' => $factory->identifier('wiki page'),
			'object_id' => $factory->identifier('PageB'),
			'contents' => $factory->plaintext('Hello World B'),
			'url' => $factory->identifier('PageB'),
		));

		$this->indexC = new Search_Elastic_Index($connection, 'test_index_c');
		$this->indexC->destroy();
		$factory = $this->indexC->getTypeFactory();
		$this->indexC->addDocument(array(
			'object_type' => $factory->identifier('wiki page'),
			'object_id' => $factory->identifier('PageB'),
			'contents' => $factory->plaintext('Hello World C'),
			'url' => $factory->identifier('/PageC'),
		));

		$connection->refresh('*');
		$connection->assignAlias('test_index_b', 'test_index_b_foo');
	}

	function testSearchAffectsAllForeign()
	{
		$query = new Search_Query('hello');
		$sub = new Search_Query('hello');

		$query->includeForeign('test_index_b', $sub);
		$query->includeForeign('test_index_c', $sub);
		$result = $query->search($this->indexA);

		$this->assertCount(3, $result);
	}

	function testResultsIndicateOriginIndex()
	{
		$query = new Search_Query('foobar');
		$sub = new Search_Query('C');

		$query->includeForeign('test_index_b', $sub);
		$query->includeForeign('test_index_c', $sub);
		$result = $query->search($this->indexA);

		$first = $result[0];
		$this->assertEquals('test_index_c', $first['_index']);
	}

	function testUnexpandAliases()
	{
		$query = new Search_Query('foobar');
		$sub = new Search_Query('B');

		$query->includeForeign('test_index_b', $sub);
		$query->includeForeign('test_index_c', $sub);
		$result = $query->search($this->indexA);

		$first = $result[0];
		// Note : test_index_b is an alias to test_index_b_...
		$this->assertEquals('test_index_b', $first['_index']);
	}

	function testTransformsApplyPerIndex()
	{
		$query = new Search_Query('Hello');
		$query->applyTransform(new Search\Federated\UrlPrefixTransform('http://foo.example.com'));
		$sub = new Search_Query('Hello');
		$sub->applyTransform(new Search\Federated\UrlPrefixTransform('http://bar.example.com/'));

		$query->includeForeign('test_index_c', $sub);
		$result = $query->search($this->indexA);

		$urls = [$result[0]['url'], $result[1]['url']];

		$this->assertContains('http://foo.example.com/PageA', $urls);
		$this->assertContains('http://bar.example.com/PageC', $urls);
	}
}


<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_FacetTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		$connection = new Search_Elastic_Connection('http://localhost:9200');

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

	function testRequireFacet()
	{
		$facet = new Search_Query_Facet_Term('categories');

		$query = new Search_Query;
		$query->filterType('wiki page');
		$query->requestFacet($facet);

		$result = $query->search($this->index);
		$values = $result->getFacet($facet);

		$this->assertEquals(
			new Search_ResultSet_FacetFilter(
				$facet,
				array(
					array('value' => 1, 'count' => 3),
					array('value' => 2, 'count' => 2),
					array('value' => 3, 'count' => 1),
					array('value' => 'orphan', 'count' => 1),
				)
			),
			$values
		);
	}

	protected function populate($index)
	{
		$this->add($index, 'ABC', array(1, 2, 3));
		$this->add($index, 'AB', array(1, 2));
		$this->add($index, 'A', array(1));
		$this->add($index, 'empty', array('orphan'));
	}

	private function add($index, $page, array $categories)
	{
		$typeFactory = $index->getTypeFactory();

		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier('wiki page'),
				'object_id' => $typeFactory->identifier($page),
				'categories' => $typeFactory->multivalue($categories),
			)
		);
	}
}


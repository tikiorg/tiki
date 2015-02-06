<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_LargeDatasetTest extends PHPUnit_Framework_TestCase
{
	protected $index;

	function setUp()
	{
		$this->index = $this->getIndex();
		$this->index->destroy();
	}

	protected function getIndex()
	{
		return new Search_MySql_Index(TikiDb::get(), 'test_index');
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}

	/**
	 * @expectedException Search_MySql_LimitReachedException
	 */
	function testManyColumns()
	{
		$typeFactory = $this->index->getTypeFactory();
		$document = array(
			'object_type' => $typeFactory->identifier('test'),
			'object_id' => $typeFactory->identifier('test'),
		);

		for ($i = 0; 1500 > $i; ++$i) {
			$document['identifier_' . $i] = $typeFactory->identifier('test');
			$document['sortable_' . $i] = $typeFactory->sortable('test');
			$document['plaintext_' . $i] = $typeFactory->plaintext('test');
		}

		$this->index->addDocument($document);
	}

	/**
	 * @expectedException Search_MySql_LimitReachedException
	 */
	function testManyIndexes()
	{
		$typeFactory = $this->index->getTypeFactory();
		$document = array(
			'object_type' => $typeFactory->identifier('test'),
			'object_id' => $typeFactory->identifier('test'),
		);

		$query = new Search_Query;
		for ($i = 0; 1000 > $i; ++$i) {
			$document['field_' . $i] = $typeFactory->sortable('test');
			$query->filterInitial('test', 'field_' . $i);
		}

		$this->index->addDocument($document);

		$query->search($this->index);
	}
}


<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 */
abstract class Search_Index_IncrementalUpdateTest extends PHPUnit_Framework_TestCase
{
	protected abstract function getIndex();

	protected function populate($index)
	{
		$this->addDocument($index, 'wiki page', 'HomePage', 'Hello World');
		$this->addDocument($index, 'wiki page', 'SomePage', 'No content yet.');
		$index->endUpdate();
	}

	function testAddNewDocument()
	{
		$index = $this->getIndex();
		$index->invalidateMultiple(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'NewPage',
				),
			)
		);
		$this->addDocument($index, 'wiki page', 'NewPage', 'Testing search');
		$index->endUpdate();

		$this->assertResultFound('search', $index);
		$this->assertResultFound('content', $index);
		$this->assertResultFound('world', $index);
	}

	function testReplaceDocument()
	{
		$index = $this->getIndex();
		$index->invalidateMultiple(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'SomePage',
				),
			)
		);
		$this->addDocument($index, 'wiki page', 'SomePage', 'Foobar');
		$index->endUpdate();

		$this->assertResultFound('foobar', $index);
		$this->assertResultFound('content', $index, 0);
	}

	function testRemoveDocument()
	{
		$index = $this->getIndex();
		$index->invalidateMultiple(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'SomePage',
				),
			)
		);
		$index->endUpdate();

		$this->assertResultFound('foobar', $index, 0);
		$this->assertResultFound('content', $index, 0);
	}

	private function assertResultFound($word, $index, $count = 1)
	{
		$query = new Search_Query($word);
		$result = $query->search($index);

		$this->assertEquals($count, count($result));
	}

	private function addDocument($index, $type, $id, $data)
	{
		$typeFactory = $index->getTypeFactory();

		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier($type),
				'object_id' => $typeFactory->identifier($id),
				'wiki_content' => $typeFactory->wikitext($data),
				'contents' => $typeFactory->wikitext($data),
			)
		);
	}
}


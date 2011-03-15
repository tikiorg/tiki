<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 */
class Search_Index_LuceneSortTest extends PHPUnit_Framework_TestCase
{
	private $dir;
	private $index;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir);
		$this->add($index, 'A', '1', 'Hello');
		$this->add($index, 'B', '10', 'foobar');
		$this->add($index, 'C', '2', 'Baz');

		$this->index = $index;
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function sortCases()
	{
		return array(
			array('numeric_field_nasc', 'ACB'),
			array('numeric_field_ndesc', 'BCA'),
			array('numeric_field_asc', 'ABC'),
			array('text_field_asc', 'CBA'),
			array('text_field_desc', 'ABC'),
			array('text_field_nasc', 'ABC'),
			array('text_field_ndesc', 'ABC'),
		);
	}

	/**
	 * @dataProvider sortCases
	 */
	function testOrdering($mode, $expected)
	{
		$query = new Search_Query;
		$query->filterType('wiki page');
		$query->setOrder($mode);

		$results = $query->search($this->index);

		$str = '';
		foreach ($results as $row) {
			$str .= $row['object_id'];
		}
	
		$this->assertEquals($expected, $str);
	}

	private function add($index, $page, $numeric, $text)
	{
		$typeFactory = $index->getTypeFactory();

		$index->addDocument(array(
			'object_type' => $typeFactory->identifier('wiki page'),
			'object_id' => $typeFactory->identifier($page),
			'numeric_field' => $typeFactory->sortable($numeric),
			'text_field' => $typeFactory->sortable($text),
		));
	}
}


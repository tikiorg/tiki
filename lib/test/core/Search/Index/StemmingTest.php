<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 */
abstract class Search_Index_StemmingTest extends PHPUnit_Framework_TestCase
{
	protected $index;

	protected function populate($index)
	{
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier('wikipage?!'),
				'object_id' => $typeFactory->identifier('Comité Wiki'),
				'description' => $typeFactory->plaintext('a descriptions for the pages éducation Case'),
				'contents' => $typeFactory->plaintext('a descriptions for the pages éducation Case'),
				'hebrew' => $typeFactory->plaintext('מחשב הוא מכונה המעבדת נתונים על פי תוכנית, כלומר על פי רצף פקודות נתון מראש. מחשבים הם חלק בלתי נפרד מחיי היומיום '),
			)
		);
	}

	function testSearchWithAdditionalS()
	{
		$query = new Search_Query('description');

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

	function testSearchAccentExactMatch()
	{
		$query = new Search_Query('éducation');

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

	function testFilterIdentifierExactly()
	{
		$query = new Search_Query;
		$query->filterType('wikipage?!');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testSearchObject()
	{
		$query = new Search_Query;
		$query->addObject('wikipage?!', 'Comité Wiki');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testStopWords()
	{
		$query = new Search_Query('a for the');
		$this->assertEquals(0, count($query->search($this->index)));
	}

	function testHebrewString()
	{
		$query = new Search_Query;
		$query->filterContent('מחשב', 'hebrew');
		$this->assertEquals(1, count($query->search($this->index)));
	}
}


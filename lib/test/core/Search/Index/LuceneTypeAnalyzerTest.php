<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: LuceneIncrementalUpdateTest.php 46131 2013-06-01 18:27:52Z changi67 $

/**
 * @group unit
 */
class Search_Index_LuceneTypeAnalyzerTest extends PHPUnit_Framework_TestCase
{
	private $dir;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();
	}

	protected function getIndex()
	{
		return new Search_Index_Lucene($this->dir);
	}

	function tearDown()
	{
		$this->getIndex()->destroy();
	}

	function testIdentifierTypes()
	{
		$index = $this->getIndex();
		$typeFactory = $index->getTypeFactory();
		$index = new Search_Index_TypeAnalysisDecorator($index);

		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier('wiki page'),
				'object_id' => $typeFactory->identifier('X'),
				'a' => $typeFactory->plaintext('X'),
				'b' => $typeFactory->wikitext('X'),
				'c' => $typeFactory->timestamp('X'),
				'd' => $typeFactory->identifier('X'),
				'e' => $typeFactory->numeric('X'),
				'f' => $typeFactory->multivalue('X'),
				'g' => $typeFactory->sortable('X'),
			)
		);

		$this->assertEquals(array('object_type', 'object_id', 'd', 'e'), $index->getIdentifierFields());
	}
}


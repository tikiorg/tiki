<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 */
abstract class Search_Index_TypeAnalyzerTest extends PHPUnit_Framework_TestCase
{
	protected abstract function getIndex();

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


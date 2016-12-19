<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_TypeAnalyzerTest extends PHPUnit_Framework_TestCase
{
	function mappingProvider()
	{
		return array(
			'empty' => array('plaintext', 'test', ''),
			'text' => array('plaintext', 'test', 'Hello World!'),
			'basic_array' => array('multivalue', 'test', array('A', 'B', 'C')),
			'map' => array('', 'test', array(
				'A' => 1,
				'B' => 2,
				'C' => 5,
			)),
			'complex' => array('', 'test', array(
				array(1, 2, 3),
				array(2, 3, 4),
			)),
			'identifier_suffix' => array('identifier', 'some_id', 'foobar'),
			'identifier_suffix2' => array('identifier', 'someId', 'foobar'),
			'date_suffix' => array('timestamp', 'modification_date', 'foobar'),
			'standard_field_type' => array('identifier', 'type', 'foobar'),
			'standard_field_object' => array('identifier', 'object', 'foobar'),
			'standard_field_version' => array('identifier', 'version', 'foobar'),
			'standard_field_user' => array('identifier', 'user', 'foobar'),
			'wiki_field' => array('wikitext', 'field_wiki', 'foobar'),
		);
	}

	/**
	 * @dataProvider mappingProvider
	 */
	function testMapping($expectedType, $key, $value)
	{
		$analyzer = new Search_Type_Analyzer;

		$this->assertEquals($expectedType, $analyzer->findType($key, $value));
	}
}


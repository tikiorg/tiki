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
		return [
			'empty' => ['plaintext', 'test', ''],
			'text' => ['plaintext', 'test', 'Hello World!'],
			'basic_array' => ['multivalue', 'test', ['A', 'B', 'C']],
			'map' => ['', 'test', [
				'A' => 1,
				'B' => 2,
				'C' => 5,
			]],
			'complex' => ['', 'test', [
				[1, 2, 3],
				[2, 3, 4],
			]],
			'identifier_suffix' => ['identifier', 'some_id', 'foobar'],
			'identifier_suffix2' => ['identifier', 'someId', 'foobar'],
			'date_suffix' => ['timestamp', 'modification_date', 'foobar'],
			'standard_field_type' => ['identifier', 'type', 'foobar'],
			'standard_field_object' => ['identifier', 'object', 'foobar'],
			'standard_field_version' => ['identifier', 'version', 'foobar'],
			'standard_field_user' => ['identifier', 'user', 'foobar'],
			'wiki_field' => ['wikitext', 'field_wiki', 'foobar'],
		];
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

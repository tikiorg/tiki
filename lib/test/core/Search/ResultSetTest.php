<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ResultSetTest extends PHPUnit_Framework_TestCase
{
	function testResultSetLocationPrefix()
	{
		$resultset = Search_ResultSet::create([
			['object_type' => 'wiki page', 'object_id' => 'Page A', 'url' => 'Page_A', '_index' => 'tiki_main'],
			['object_type' => 'wiki page', 'object_id' => 'Page B', 'url' => 'Page_B', '_index' => 'foreign_main'],
		]);
		$resultset->applyTransform(function ($entry) {
			return new Search_Elastic_Transform_UrlPrefix($entry, [
				'foreign_main' => 'http://example.com/',
			]);
		});

		$this->assertEquals($resultset[0]['url'], 'Page_A');
		$this->assertEquals($resultset[1]['url'], 'http://example.com/Page_B');
	}
}


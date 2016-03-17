<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_BaseTest extends Search_Index_BaseTest
{
	function setUp()
	{
		static $count = 0;

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

	function testIndexProvidesHighlightHelper()
	{
		$query = new Search_Query('foobar or bonjour');
		$resultSet = $query->search($this->index);

		$plugin = new Search_Formatter_Plugin_WikiTemplate('{display name=highlight}');
		$formatter = new Search_Formatter($plugin);
		$output = $formatter->format($resultSet);

		$this->assertContains('<em>Bonjour</em>', $output);
		$this->assertNotContains('<body>', $output);
	}
}

